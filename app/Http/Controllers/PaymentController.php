<?php 
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request; // Added for type-hinting if needed

class PaymentController extends Controller
{
    public function purchases()
    {
        // withTrashed() here is correct. 
        // Ensure Product and Stock models have "use SoftDeletes;" inside them.
        $payments = Payment::with([
            'product' => fn($q) => $q->withTrashed(),
        ])
        ->where('user_id', auth()->id())
        ->where('status', 'succeeded')
        ->latest()
        ->get();

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
                                'amount' => (int) ($product->price * 100), // Convert to cents
                                'name' => $product->name,
                                'quantity' => 1,
                            ]
                        ],
                        'payment_method_types' => ['qrph', 'gcash', 'paymaya'], // Added more local options
                        'success_url' => url('/payment/success'),
                        'cancel_url' => url('/payment/failed'),
                        'metadata' => [
                            'user_id' => (string) auth()->id(),
                            'product_id' => (string) $product->id,
                            'referrer_id' => (string) request()->cookie('referrer_id', ''), 
                        ]
                    ]
                ]
            ]);

        if ($response->successful()) {
            $data = $response->json('data');

            Payment::create([
                'user_id' => auth()->id(),
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