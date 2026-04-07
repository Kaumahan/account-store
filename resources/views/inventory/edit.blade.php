@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#0a0e18] py-12 px-6 text-white">
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <h1 class="text-xl font-black uppercase tracking-widest text-emerald-500">Edit Listing</h1>
            <p class="text-gray-500 text-xs">Modifying Asset: {{ $product->name }}</p>
        </div>

        <div class="bg-[#151a26] border border-gray-800 rounded-3xl p-8 shadow-2xl">
            <form action="{{ route('inventory.update', $product->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="text-[10px] font-black text-gray-500 uppercase mb-2 block">Product Name</label>
                    <input name="name" type="text" value="{{ $product->name }}" 
                           class="w-full bg-[#0d121d] border border-gray-800 rounded-xl py-3 px-4 text-white focus:border-emerald-500 outline-none transition-all">
                </div>

                <div>
                    <label class="text-[10px] font-black text-gray-500 uppercase mb-2 block">Base Price (PHP)</label>
                    <input name="price" type="number" step="0.01" value="{{ $product->price }}" 
                           class="w-full bg-[#0d121d] border border-gray-800 rounded-xl py-3 px-4 text-white focus:border-emerald-500 outline-none transition-all">
                </div>

                <div class="flex items-center justify-between p-4 bg-[#0d121d] rounded-2xl border border-gray-800">
                    <span class="text-[10px] font-black text-gray-500 uppercase">Listing Status</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>

                <div class="flex gap-4 pt-4">
                    <a href="{{ route('stocks.index') }}" class="flex-1 text-center bg-white/5 hover:bg-white/10 text-gray-400 py-4 rounded-2xl text-[10px] uppercase font-black tracking-widest transition-all">
                        Cancel
                    </a>
                    <button type="submit" class="flex-2 w-full bg-emerald-600 hover:bg-emerald-500 text-white font-black py-4 rounded-2xl text-[10px] uppercase tracking-[0.2em] transition-all">
                        Update Listing
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection