<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Events\MessageSent; // For your Chat/Notification

class PayMongoWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->input('data');
        $attributes = $data['attributes'];

        // PayMongo sends the checkout session ID
        $sessionId = $data['id'];

        // Check the Payment Intent status inside the session
        $intentStatus = $attributes['payment_intent']['attributes']['status'] ?? null;

        if ($intentStatus === 'succeeded') {
            $payment = Payment::where('checkout_session_id', $sessionId)->first();

            if ($payment && $payment->status !== 'succeeded') {
                // 1. Update Database
                $payment->update([
                    'status' => 'succeeded',
                    'payment_method' => $attributes['payment_method_used'] ?? 'gcash',
                ]);

                // 2. Award Points (Check if referrer_id is not empty)
                $refId = $attributes['metadata']['referrer_id'] ?? null;
                if (!empty($refId)) {
                    Redis::incrby("user:{$refId}:points", 1500);
                }

                // 3. Trigger Notification via Reverb
                broadcast(new \App\Events\MessageSent('System', 'Payment Verified!'))->toOthers();
            }
        }

        return response()->json(['status' => 'ok']);
    }
}