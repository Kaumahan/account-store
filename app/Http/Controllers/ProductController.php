<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    //
    public function index()
    {
        // In a real app, we'd cache this in Redis using Octane
        $products = \App\Models\Product::where('is_active', true)->get();
        
        return view('welcome', compact('products'));
    }
}
