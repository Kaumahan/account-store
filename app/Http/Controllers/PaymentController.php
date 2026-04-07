<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function purchases()
    {
        $payments = Payment::with(['product', 'stock'])
            ->where('user_id', auth()->id())
            ->where('status', 'succeeded')
            ->latest()
            ->get();

        // Check if the stock is attached:
        // dd($payments->first()->stock); 

        return view('purchases', compact('payments'));
    }
    public function checkout(Product $product)
    {
        $secretKey = config('services.paymongo.secret_key');

        $response = Http::withBasicAuth($secretKey, '')
            ->post('https://api.paymongo.com/v1/checkout_sessions', [
                'data' => [
                    'attributes' => [
                        'line_items' => [
                            [
                                'currency' => 'PHP',
                                'amount' => (int) ($product->price * 100),
                                'name' => $product->name,
                                'quantity' => 1,
                            ]
                        ],
                        'payment_method_types' => ['qrph'],
                        'success_url' => url('/payment/success'),
                        'cancel_url' => url('/payment/failed'),
                        'metadata' => [
                            'user_id' => auth()->id(),
                            'product_id' => $product->id,
                            'referrer_id' => request()->cookie('referrer_id'), // For your ₱15 points
                        ]
                    ]
                ]
            ]);

        if ($response->successful()) {
            $data = $response->json('data');

            // Record the attempt in our database
            Payment::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'checkout_session_id' => $data['id'],
                'amount' => $product->price,
                'status' => 'pending',
            ]);

            return redirect($data['attributes']['checkout_url']);
        }

        return back()->withErrors('Gateway error.');
    }
}