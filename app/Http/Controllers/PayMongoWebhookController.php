<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Log;

class PayMongoWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $signature = $request->header('paymongo-signature');
        $webhookSecret = env('PAYMONGO_WEBHOOK_SECRET');
        $payload = $request->getContent();

        // 1. Log incoming request for debugging
        Log::info('PayMongo Webhook Received', [
            'header' => $signature,
            'payload' => json_decode($payload, true)
        ]);

        if (!$signature || !$webhookSecret) {
            Log::error('PayMongo Webhook Error: Missing signature or secret.');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // 2. Verify Signature
        if (!$this->isSignatureValid($signature, $payload, $webhookSecret)) {
            Log::warning('PayMongo Webhook Warning: Invalid signature verification.');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = $request->input('data');
        $attributes = $data['attributes'];

        // PayMongo Webhooks nest the resource inside 'data' -> 'attributes' -> 'data'
        $resource = $attributes['data'] ?? null;

        if (!$resource) {
            Log::error('PayMongo Webhook Error: Resource data missing from payload.');
            return response()->json(['error' => 'Malformed data'], 400);
        }

        $sessionId = $resource['id'];
        $intentStatus = $resource['attributes']['payment_intent']['attributes']['status'] ?? null;

        Log::info("Processing Session: {$sessionId} | Status: {$intentStatus}");

        if ($intentStatus === 'succeeded') {
            $payment = Payment::where('checkout_session_id', $sessionId)->first();

            if (!$payment) {
                Log::error("PayMongo Webhook Error: Payment record not found for Session ID: {$sessionId}");
                return response()->json(['status' => 'not_found'], 404);
            }

            if ($intentStatus === 'succeeded') {
                $payment = Payment::where('checkout_session_id', $sessionId)->first();

                if (!$payment) {
                    Log::error("PayMongo Webhook Error: Payment record not found for Session ID: {$sessionId}");
                    return response()->json(['status' => 'not_found'], 404);
                }

                if ($payment->status !== 'succeeded') {
                    $productId = $payment->product_id;

                    // Use a Database Transaction for data integrity
                    \DB::transaction(function () use ($payment, $productId, $resource) {

                        // 1. Update the Payment record
                        $payment->update([
                            'status' => 'succeeded',
                            'payment_method' => $resource['attributes']['payment_method_used'] ?? 'unknown',
                        ]);

                        // 2. Decrement the general Product stock count
                        Product::where('id', $productId)->decrement('stock');

                        // 3. Find and link ONE specific Stock entity (the account/item)
                        $stock = Stock::where('product_id', $productId)
                            ->whereNull('payment_id') // Ensure it hasn't been sold yet
                            ->lockForUpdate()         // Prevent race conditions
                            ->first();

                        if ($stock) {
                            $stock->update([
                                'payment_id' => $payment->id,
                                'is_sold' => true
                            ]);
                            Log::info("Middleguy: Stock ID {$stock->id} successfully linked to Payment {$payment->id}");
                        } else {
                            Log::error("Middleguy CRITICAL: Payment {$payment->id} succeeded but NO STOCK available for Product {$productId}");
                        }
                    });

                    // Trigger real-time notification
                    broadcast(new \App\Events\MessageSent('System', 'Payment Verified!'))->toOthers();
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    private function isSignatureValid($header, $payload, $secret)
    {
        try {
            // 1. Parse the header into an associative array
            $signatureData = [];
            foreach (explode(',', $header) as $part) {
                if (str_contains($part, '=')) {
                    [$key, $value] = explode('=', $part, 2);
                    $signatureData[trim($key)] = trim($value);
                }
            }

            $timestamp = $signatureData['t'] ?? null;

            // 2. PayMongo uses 'v1' for live and 'te' for test mode signatures
            $providedSignature = $signatureData['v1'] ?? $signatureData['te'] ?? null;

            if (!$timestamp || !$providedSignature) {
                Log::error('Signature components missing from header');
                return false;
            }

            // 3. Create the base string and hash it
            $baseString = $timestamp . '.' . $payload;
            $hash = hash_hmac('sha256', $baseString, $secret);

            return hash_equals($hash, $providedSignature);
        } catch (\Exception $e) {
            Log::error('Signature Helper Error: ' . $e->getMessage());
            return false;
        }
    }
}