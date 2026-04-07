@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-[#0a0e18] py-12 px-6 font-sans antialiased text-white">
        <div class="max-w-6xl mx-auto" x-data="{ 
                                basePrice: 0,
                                get gas() { 
                                    return this.basePrice > 0 ? 10 : 0 
                                },
                                get total() { 
                                    return parseFloat(this.basePrice || 0) + this.gas 
                                }
                             }">

            <div class="flex items-center justify-between mb-10">
                <div>
                    <h1 class="text-2xl font-black uppercase tracking-[0.3em] text-emerald-500">Inventory Hub</h1>
                    <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mt-1">Global Stock & Asset
                        Management</p>
                </div>
                <div class="bg-emerald-500/10 border border-emerald-500/20 px-4 py-2 rounded-xl">
                    <span class="text-[10px] text-emerald-500 font-black uppercase">Encryption Active</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-1 space-y-6">

                    <div class="bg-[#151a26] border border-emerald-500/30 rounded-3xl p-6 shadow-2xl"
                        x-data="{ imageUrl: '' }">
                        <h2 class="text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-6">
                            Create Product Listing
                        </h2>

                        <form action="{{ route('products.store') }}" method="POST" class="space-y-5">
                            @csrf

                            <div>
                                <label class="text-[10px] font-black text-gray-500 uppercase mb-2 block">Product
                                    Name</label>
                                <input name="name" type="text" placeholder="e.g. Valorant Account"
                                    class="w-full bg-[#0d121d] border border-gray-800 rounded-xl py-3 px-4 text-sm text-white focus:border-emerald-500 outline-none transition-all">
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-gray-500 uppercase mb-2 block">Product Image
                                    URL</label>
                                <div class="relative">
                                    <input x-model="imageUrl" name="image_url" type="url"
                                        placeholder="https://imgur.com/image.png"
                                        class="w-full bg-[#0d121d] border border-gray-800 rounded-xl py-3 px-4 text-sm text-white focus:border-emerald-500 outline-none transition-all">

                                    <template x-if="imageUrl">
                                        <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                            <img :src="imageUrl"
                                                class="h-8 w-8 object-cover rounded-lg border border-emerald-500/50 shadow-lg shadow-emerald-500/20">
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-gray-500 uppercase mb-2 block">Product
                                    Details</label>
                                <textarea name="details" rows="3" placeholder="user:pass"
                                    class="w-full bg-[#0d121d] border border-gray-800 rounded-xl py-3 px-4 text-xs text-white outline-none resize-none"></textarea>
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-gray-500 uppercase mb-2 block">Base Price
                                    (PHP)</label>
                                <input x-model="basePrice" type="number" step="0.01" placeholder="0.00"
                                    class="w-full bg-[#0d121d] border border-gray-800 rounded-xl py-3 px-4 text-sm text-white focus:border-emerald-500 outline-none transition-all">
                            </div>

                            <input type="hidden" name="price" :value="total">

                            <div class="bg-[#0a0e18] rounded-2xl p-4 border border-gray-800 space-y-3">
                                <div class="flex justify-between text-[10px] font-bold uppercase tracking-tighter">
                                    <span class="text-gray-500">Gas Fee</span>
                                    <span class="text-cyan-500 font-mono">+ ₱10.00</span>
                                </div>
                                <div class="pt-3 border-t border-gray-800 flex justify-between items-end">
                                    <span class="text-[10px] font-black text-emerald-500 uppercase">Buyer Total</span>
                                    <span class="text-xl font-black text-white font-mono">₱<span
                                            x-text="total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></span>
                                </div>
                            </div>

                            <button type="submit"
                                class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-black py-4 rounded-2xl text-[10px] uppercase tracking-[0.2em] transition-all active:scale-95 shadow-lg shadow-emerald-900/40">
                                Deploy Listing
                            </button>
                        </form>
                    </div>

                    <div
                        class="bg-[#151a26] border border-gray-800 rounded-3xl p-6 shadow-2xl opacity-60 hover:opacity-100 transition-opacity">
                        <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6 italic text-center">
                            Secure Deposit</h2>
                        <form action="{{ route('stocks.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <select name="product_id"
                                class="w-full bg-[#0d121d] border border-gray-800 rounded-xl py-3 px-4 text-xs text-white outline-none">
                                <option value="" disabled selected>Select Target...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            <textarea name="data" rows="3" placeholder="user:pass"
                                class="w-full bg-[#0d121d] border border-gray-800 rounded-xl py-3 px-4 text-xs text-white outline-none resize-none"></textarea>
                            <button type="submit"
                                class="w-full bg-white/5 hover:bg-white/10 text-gray-400 border border-white/5 font-black py-3 rounded-xl text-[9px] uppercase tracking-widest transition-all">
                                Add Assets
                            </button>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-8">

                    <div class="bg-[#151a26]/40 border border-gray-800 rounded-3xl overflow-hidden shadow-2xl">
                        <div class="p-6 border-b border-gray-800 flex justify-between items-center bg-[#151a26]">
                            <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest text-emerald-500">
                                Active Inventory Listings</h2>
                            <span class="text-[9px] text-gray-500 font-bold uppercase tracking-widest">Manage Items</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-[#0d121d]/50">
                                    <tr class="border-b border-gray-800/50 text-[9px] font-black text-gray-500 uppercase">
                                        <th class="px-6 py-4">Product Name</th>
                                        <th class="px-6 py-4 text-right text-emerald-400/50">Market Price</th>
                                        <th class="px-6 py-4 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-800/30">
                                    @foreach($products as $product)
                                        <tr class="hover:bg-white/5 transition-colors group">
                                            <td class="px-6 py-4">
                                                <div class="text-[13px] font-bold text-gray-200">{{ $product->name }}</div>
                                                <div class="text-[10px] text-gray-500 font-mono uppercase">Base:
                                                    ₱{{ number_format($product->price, 2) }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-right font-mono text-emerald-400 font-bold">
                                                ₱{{ number_format(($product->price * 1.035) + 15 + 5, 2) }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center justify-center gap-3">
                                                    <a href="{{ route('inventory.edit', $product->id) }}"
                                                        class="p-2 bg-blue-500/10 text-blue-400 rounded-lg hover:bg-blue-500 hover:text-white transition-all shadow-sm"
                                                        title="Edit Listing">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>

                                                    <form action="{{ route('inventory.destroy', $product->id) }}" method="POST"
                                                        onsubmit="return confirm('Permanently remove this listing from the hub?');"
                                                        class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="p-2 bg-red-500/10 text-red-400 rounded-lg hover:bg-red-500 hover:text-white transition-all shadow-sm"
                                                            title="Delete Listing">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-[#151a26]/40 border border-gray-800 rounded-3xl overflow-hidden">
                        <div class="p-6 border-b border-gray-800 flex justify-between items-center bg-[#151a26]">
                            <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Recent Stock Ingress
                            </h2>
                            <span class="text-[9px] text-emerald-500 font-bold bg-emerald-500/10 px-2 py-1 rounded">Live
                                Feed</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="border-b border-gray-800/50 text-[9px] font-black text-gray-500 uppercase">
                                        <th class="px-6 py-4">ID</th>
                                        <th class="px-6 py-4">Product</th>
                                        <th class="px-6 py-4 text-center">Status</th>
                                        <th class="px-6 py-4">Added</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-800/30">
                                    @foreach($recentStocks as $stock)
                                        <tr class="hover:bg-white/5 transition-colors group">
                                            <td class="px-6 py-4 text-[11px] font-mono text-gray-500 italic">#{{ $stock->id }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-[12px] font-bold text-gray-300">
                                                    {{ $stock->product->name ?? ""}}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span
                                                    class="px-2 py-1 rounded text-[9px] font-black uppercase {{ $stock->is_sold ? 'text-red-400 bg-red-400/10' : 'text-emerald-400 bg-emerald-400/10' }}">
                                                    {{ $stock->is_sold ? 'Sold' : 'Secure' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-[10px] text-gray-500 font-medium italic">
                                                {{ $stock->created_at->diffForHumans() }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection