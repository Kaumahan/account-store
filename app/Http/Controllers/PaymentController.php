<?php 

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Octane Tip: Database queries are fast, but ensure you are not 
     * storing the auth() user in a static property.
     */
    public function purchases(Request $request): View
    {
        $payments = Payment::query()
            ->with(['product' => fn($q) => $q->withTrashed()])
            ->where('user_id', $request->user()->id) // Use $request->user() over auth() helper
            ->where('status', 'succeeded')
            ->latest()
            ->get();

        return view('purchases', compact('payments'));
    }

    /**
     * Octane Tip: External HTTP calls can bottleneck workers. 
     * Consider using Octane::concurrently if you had multiple API calls.
     */
    public function checkout(Request $request, Product $product): RedirectResponse
    {
        // Cache config lookup if this is called frequently
        $secretKey = config('services.paymongo.secret_key');
        $userId = $request->user()->id;

        $response = Http::withBasicAuth($secretKey, '')
            ->timeout(10) // Always set a timeout so you don't hang an Octane worker
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
                        'payment_method_types' => ['qrph', 'gcash', 'paymaya'],
                        'success_url' => url('/payment/success'),
                        'cancel_url' => url('/payment/failed'),
                        'metadata' => [
                            'user_id' => (string) $userId,
                            'product_id' => (string) $product->id,
                            'referrer_id' => (string) $request->cookie('referrer_id', ''), 
                        ]
                    ]
                ]
            ]);

        if ($response->successful()) {
            $data = $response->json('data');

            // DB operations are fine, Octane handles connection pooling
            Payment::create([
                'user_id' => $userId,
                'product_id' => $product->id,
                'checkout_session_id' => $data['id'],
                'amount' => $product->price,
                'status' => 'pending',
            ]);

            return redirect($data['attributes']['checkout_url']);
        }

        return back()->withErrors('Payment gateway is currently unavailable.');
    }
}