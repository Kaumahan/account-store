<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Events\MessageSent;

class PayMongoWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $signature = $request->header('paymongo-signature');
        $webhookSecret = env('PAYMONGO_WEBHOOK_SECRET');
        $payload = $request->getContent();

        Log::info('[Webhook] Raw Payload Received', ['payload' => json_decode($payload, true)]);

        if (!$signature || !$webhookSecret) {
            Log::error('[Webhook] Missing signature or secret.');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (!$this->isSignatureValid($signature, $payload, $webhookSecret)) {
            Log::warning('[Webhook] Invalid signature verification.');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = $request->input('data');
        $resource = $data['attributes']['data'] ?? null;

        if (!$resource) {
            Log::error('[Webhook] Resource data missing from payload.');
            return response()->json(['error' => 'Malformed data'], 400);
        }

        $sessionId = $resource['id'];
        $intentStatus = $resource['attributes']['payment_intent']['attributes']['status'] ?? null;

        Log::info("[Webhook] Step 1: Data Parsed", ['session_id' => $sessionId, 'status' => $intentStatus]);

        try {
            if ($intentStatus === 'succeeded') {
                Log::info("[Webhook] Step 2: Intent Succeeded. Fetching Payment...");

                $payment = Payment::with('product.user')->where('checkout_session_id', $sessionId)->first();

                if (!$payment) {
                    Log::error("[Webhook] ERROR: Payment record not found in DB", ['session_id' => $sessionId]);
                    return response()->json(['status' => 'not_found'], 404);
                }

                Log::info("[Webhook] Step 3: Payment Found", [
                    'payment_id' => $payment->id,
                    'current_db_status' => $payment->status,
                    'product_id' => $payment->product_id
                ]);

                if ($payment->status !== 'succeeded') {
                    Log::info("[Webhook] Step 4: Searching for available Stock...");

                    $stock = Stock::where('product_id', $payment->product_id)
                        ->whereNull('payment_id')
                        ->first();

                    if ($stock) {
                        Log::info("[Webhook] Step 5: Stock Found", ['stock_id' => $stock->id]);

                        // A. Update Payment
                        $payment->update([
                            'status' => 'succeeded',
                            'payment_method' => $resource['attributes']['payment_method_used'] ?? 'gcash',
                        ]);
                        Log::info("[Webhook] Step 6: Payment marked as succeeded");

                        // B. Link Stock
                        $stock->update([
                            'payment_id' => $payment->id,
                            'is_sold' => true
                        ]);
                        //   Log::info("[Webhook] Step 7: Stock linked to payment");

                        // C. Handle Product and Seller
                        if ($payment->product) {
                            $payment->product->decrement('stock');
                            //   Log::info("[Webhook] Step 8: Product stock decremented");

                            $seller = $payment->product->user;
                            Log::info("Seller data {$seller}");
                            // FALLBACK: If product has no owner, check metadata
                            if (!$seller) {
                                Log::info("[Webhook] Product has no user_id, checking metadata fallback...");
                                $metadata = $resource['attributes']['metadata'] ?? [];
                                $sellerIdFromMetadata = $metadata['user_id'] ?? null;

                                if ($sellerIdFromMetadata) {
                                    $seller = User::find($sellerIdFromMetadata);
                                }
                            }

                            if ($seller) {
                                $earnedAmount = $payment->amount; // Convert cents to Pesos
                                $seller->increment('current_balance', $earnedAmount);

                                Log::info("[Webhook] Step 9: Balance Updated", [
                                    'user' => $seller->id,
                                    'new_funds' => $earnedAmount
                                ]);
                            } else {
                                //  Log::error("[Webhook] CRITICAL: No seller found in Product OR Metadata. Points lost.");
                            }
                        } else {
                            //Log::warning("[Webhook] WARNING: Payment has no associated product");
                        }

                        //Log::info("[Webhook] SUCCESS: Process complete for Session {$sessionId}");

                    } else {
                        //Log::error("[Webhook] CRITICAL ERROR: No available stock for Product ID: " . $payment->product_id);
                    }
                } else {
                    //Log::info("[Webhook] SKIP: Payment was already processed previously.");
                }
            } else {
                //Log::info("[Webhook] SKIP: Intent status is not 'succeeded' (" . $intentStatus . ")");
            }
        } catch (\Exception $e) {
            Log::error("[Webhook] EXCEPTION: " . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }

        return response()->json(['status' => 'ok']);
    }

    private function isSignatureValid($header, $payload, $secret)
    {
        try {
            $signatureData = [];
            foreach (explode(',', $header) as $part) {
                if (str_contains($part, '=')) {
                    [$key, $value] = explode('=', $part, 2);
                    $signatureData[trim($key)] = trim($value);
                }
            }

            $timestamp = $signatureData['t'] ?? null;

            // PayMongo uses 'li' for live, 'te' for test, or 'v1' in some implementations
            $providedSignature = $signatureData['li'] ?? $signatureData['te'] ?? $signatureData['v1'] ?? null;

            if (!$timestamp || !$providedSignature) {
                Log::error('[Webhook Signature] Components missing', [
                    'header' => $header,
                    'parsed_keys' => array_keys($signatureData)
                ]);
                return false;
            }

            $baseString = $timestamp . '.' . $payload;
            $hash = hash_hmac('sha256', $baseString, $secret);

            return hash_equals($hash, $providedSignature);
        } catch (\Exception $e) {
            Log::error('[Webhook Signature] Error: ' . $e->getMessage());
            return false;
        }
    }
}