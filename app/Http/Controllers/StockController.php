<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Import this at the top

class StockController extends Controller
{

    // app/Http/Controllers/StockController.php

    public function index()
    {
        // 1. Fetch active products for the dropdown
        $products = Product::orderBy('name')->get();

        // 2. Fetch recent stocks ONLY if their parent product is NOT deleted
        $recentStocks = Stock::whereHas('product') // This is the "Trash Filter"
            ->with('product')
            ->latest()
            ->take(10)
            ->get();

        return view('stocks.index', compact('products', 'recentStocks'));
    }
    
    public function store(Request $request)
    {
        // 1. Determine if we are creating a product OR adding to one
        $isNewProduct = $request->has('name');

        $request->validate([
            'product_id' => $isNewProduct ? 'nullable' : 'required|exists:products,id',
            'name' => $isNewProduct ? 'required|string|max:255' : 'nullable',
            'price' => $isNewProduct ? 'required|numeric' : 'nullable',
            'data' => 'required|string',
        ]);

        // 2. If it's a new product, create it first
        if ($isNewProduct) {
            $product = Product::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . Str::random(5), // Added random string to avoid duplicates
                'price' => $request->price,
                'stock' => 0,
            ]);
            $productId = $product->id;
        } else {
            $productId = $request->product_id;
        }

        // 3. Create the Stock entry
        Stock::create([
            'product_id' => $productId,
            'data' => $request->data,
            'is_sold' => false,
        ]);

        // 4. Increment the product's count
        Product::find($productId)->increment('stock');

        return back()->with('success', 'Asset deployed to the hub.');
    }
}