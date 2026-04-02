<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function checkout(Product $product)
    {
        // 1. Get Secret Key
        $secretKey = config('services.paymongo.secret_key');

        // 2. Make the Request
        // Added the missing '->' before withBasicAuth
        $response = Http::withoutVerifying()
            ->withBasicAuth($secretKey, '') 
            ->post('https://api.paymongo.com/v1/checkout_sessions', [
                'data' => [
                    'attributes' => [
                        'line_items' => [
                            [
                                'currency' => 'PHP',
                                'amount'   => (int) ($product->price * 100), 
                                'name'     => $product->name,
                                'quantity' => 1,
                            ]
                        ],
                        'payment_method_types' => ['gcash', 'paymaya', 'card'],
                        'success_url' => url('/'), 
                        'cancel_url'  => url('/'),
                    ]
                ]
            ]);

        // 3. Handle Errors
        if ($response->failed()) {
            logger()->error('PayMongo Checkout Failed', [
                'status' => $response->status(),
                'error'  => $response->json()
            ]);
            return back()->withErrors('Payment gateway is currently unavailable.');
        }

        // 4. Redirect to PayMongo
        return redirect($response->json('data.attributes.checkout_url'));
    }
}