<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Octane\Facades\Octane;

class ProductController extends Controller
{
    /**
     * Marketplace Home - Optimized with Octane Concurrency
     * Great for when you eventually add categories or featured banners.
     */
    public function index()
    {
        // Even if it's just one query now, using Octane's mindset 
        // prepares you for sub-millisecond response times.
        [$products] = Octane::concurrently([
            fn () => Product::where('is_active', true)
                ->where('stock', '>', 0)
                ->latest()
                ->limit(20) // Always limit for memory efficiency in Octane
                ->get(),
        ]);

        return view('welcome', compact('products'));
    }

    /**
     * Own Products - Parallel data fetching
     * We fetch products and recent activity at the exact same time.
     */
    public function ownProducts()
    {
        $user = auth()->user();

        // High-performance parallel execution
        [$products, $recentStocks] = Octane::concurrently([
            fn () => $user->products()
                ->withCount([
                    'stocks as total_stock' => function ($query) {
                        $query->where('is_sold', false);
                    }
                ])
                ->latest()
                ->get(),

            fn () => $user->stocks()
                ->with('product')
                ->latest()
                ->limit(10)
                ->get(),
        ]);

        return view('stocks.index', compact('products', 'recentStocks'));
    }

    /**
     * CREATE: Store product
     * Optimized slug generation and atomic creation.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'image_url' => 'nullable|url',
        ]);

        // Clean assignment
        $product = Product::create([
            'user_id'     => auth()->id(),
            'name'        => $data['name'],
            'price'       => $data['price'],
            'stock'       => $data['stock'] ?? 0,
            'image_url'   => $request->input('image_url'),
            'description' => $request->input('details'), // Mapping 'details' from UI to 'description'
            'slug'        => Str::slug($data['name']) . '-' . Str::random(4),
            'is_active'   => true,
        ]);

        return redirect()->route('stocks.index')->with('success', 'Listing Deployed to Terminal.');
    }

    public function edit(Product $product)
    {
        // Ensure ownership before editing
        if ($product->user_id !== auth()->id()) {
            abort(403);
        }
        
        return view('inventory.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        if ($product->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $product->update($data);

        return redirect()->route('stocks.index')->with('success', 'Listing Re-calibrated.');
    }

    public function destroy(Product $product)
    {
        if ($product->user_id !== auth()->id()) {
            abort(403);
        }

        $product->delete();

        return redirect()->route('stocks.index')->with('success', 'Listing Nuked.');
    }
}