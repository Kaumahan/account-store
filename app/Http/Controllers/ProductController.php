<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // Marketplace Home
    public function index()
    {
        $products = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->latest()
            ->get();
        return view('welcome', compact('products'));
    }

    public function ownProducts()
    {
        // Scoped specifically to the authenticated user via the relationship
        $products = auth()->user()->products()
            ->withCount([
                'stocks as total_stock' => function ($query) {
                    $query->where('is_sold', false);
                }
            ])
            ->latest()
            ->get();

        // Pulling recent activity for the log section
        $recentStocks = auth()->user()->stocks()
            ->with('product')
            ->latest()
            ->get();

        return view('stocks.index', compact('products', 'recentStocks'));
    }

    // CREATE: Store product and redirect back to Inventory
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
        ]);

        $data['user_id'] = auth()->id();
        $data['image_url'] = $request->input('image_url');
        $data['description'] = $request->input('details');
        $data['slug'] = Str::slug($data['name']) . '-' . rand(1000, 9999);
        $data['stock'] = $data['stock'] ?? 0;
        Product::create($data);

        return redirect()->route('stocks.index')->with('success', 'Listing Deployed.');
    }

    // EDIT: Show the edit page
    public function edit(Product $product)
    {
        return view('inventory.edit', compact('product'));
    }

    // UPDATE: Handle the edit form
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $product->update($data);

        return redirect()->route('stocks.index')->with('success', 'Listing Updated.');
    }

    // DELETE: Remove product from table
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('stocks.index')->with('success', 'Listing Nuked.');
    }
}