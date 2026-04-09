@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-2 space-y-8">
            {{-- Inventory Table --}}
            <div class="bg-[#151a26]/40 border border-gray-800 rounded-3xl overflow-hidden shadow-2xl">
                <div class="p-6 border-b border-gray-800 flex justify-between items-center bg-[#151a26]">
                    <h2 class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">
                        Active Inventory Listings
                    </h2>
                    <span class="text-[9px] text-gray-500 font-bold uppercase tracking-widest">Live Assets</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-[#0d121d]/50">
                            <tr class="border-b border-gray-800/50 text-[9px] font-black text-gray-500 uppercase">
                                <th class="px-6 py-4">Product Name</th>
                                <th class="px-6 py-4 text-right">Market Price</th>
                                <th class="px-6 py-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/30">
                            @foreach($products as $product)
                                <tr class="hover:bg-white/5 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="text-[13px] font-bold text-gray-200">{{ $product->name }}</div>
                                        <div class="text-[10px] text-gray-500 font-mono uppercase">ID:
                                            SEC-{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono text-emerald-400 font-bold">
                                        ₱{{ number_format($product->price, 2) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ route('inventory.edit', $product->id) }}"
                                                class="p-2 bg-blue-500/10 text-blue-400 rounded-lg hover:bg-blue-500 hover:text-white transition-all">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('inventory.destroy', $product->id) }}" method="POST"
                                                onsubmit="return confirm('Confirm Deletion?');">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 bg-red-500/10 text-red-400 rounded-lg hover:bg-red-500 hover:text-white transition-all">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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

            {{-- Log Section --}}
            <div class="bg-[#151a26]/40 border border-gray-800 rounded-3xl overflow-hidden shadow-2xl">
                <div class="p-6 border-b border-gray-800 bg-[#151a26]">
                    <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Recent Ingress Log</h2>
                </div>
                <div class="p-4 space-y-3">
                    @foreach($recentStocks as $stock)
                        <div class="flex items-center justify-between bg-[#0d121d] p-3 rounded-xl border border-gray-800/50">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-2 h-2 rounded-full {{ $stock->is_sold ? 'bg-red-500 shadow-[0_0_8px_#ef4444]' : 'bg-emerald-500 shadow-[0_0_8px_#10b981]' }}">
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold text-gray-300">{{ $stock->product->name ?? 'Unknown' }}</p>
                                    <p class="text-[9px] text-gray-500 font-mono italic">#{{ $stock->id }} •
                                        {{ $stock->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <span
                                class="text-[9px] font-black {{ $stock->is_sold ? 'text-red-400' : 'text-emerald-400' }} uppercase">
                                {{ $stock->is_sold ? 'Out' : 'In' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- PAYOUT CARD --}}
            <div class="bg-[#151a26] border-2 border-cyan-500/30 rounded-3xl p-6 shadow-[0_0_50px_-12px_rgba(6,182,212,0.2)] relative overflow-hidden group">
                
                @php 
                    $isThursday = now()->isThursday();
                    $pendingClaim = auth()->user()->redemptions()->where('status', 'pending')->first(); 
                @endphp

                {{-- Status Indicators --}}
                <div class="absolute top-0 right-0 p-4">
                    <div class="flex gap-1.5">
                        <div class="w-1.5 h-1.5 {{ $isThursday ? 'bg-emerald-500 animate-ping' : 'bg-red-500' }} rounded-full"></div>
                        <div class="w-1.5 h-1.5 {{ $isThursday ? 'bg-emerald-500/50' : 'bg-red-500/50' }} rounded-full"></div>
                    </div>
                </div>

                <div class="mb-8 text-center">
                    <p class="text-[9px] font-black text-cyan-500/60 uppercase tracking-[0.3em] mb-2">Available Credits</p>
                    <div class="text-4xl font-black text-white font-mono flex items-center justify-center gap-2">
                        <span class="text-cyan-500 text-lg">₱</span>
                        {{ number_format(auth()->user()->current_balance, 2) }}
                    </div>
                </div>

                @if($pendingClaim)
                    <div class="bg-cyan-500/5 border border-cyan-500/20 rounded-2xl p-4">
                        <div class="flex flex-col gap-3">
                            <div class="flex justify-between items-center">
                                <span class="text-[9px] font-black text-cyan-400 uppercase tracking-widest">
                                    Transferring ₱{{ number_format($pendingClaim->amount, 2) }}
                                </span>
                                <span class="text-[9px] text-cyan-400/50 font-mono">
                                    {{ $pendingClaim->created_at->diffForHumans(null, true) }}
                                </span>
                            </div>
                            
                            @php 
                                $hoursPassed = $pendingClaim->created_at->diffInHours(now());
                                $progress = min(($hoursPassed / 48) * 100, 95); 
                            @endphp

                            <div class="h-1.5 w-full bg-cyan-900/30 rounded-full overflow-hidden">
                                <div class="bg-cyan-500 h-full shadow-[0_0_10px_#06b6d4] transition-all duration-1000" 
                                    style="width: {{ $progress }}%"></div>
                            </div>
                            
                            <p class="text-[8px] text-gray-500 text-center uppercase tracking-tighter leading-tight">
                                PayMongo Settlement Verification:<br>{{ max(0, 48 - $hoursPassed) }}h estimated remaining
                            </p>
                        </div>
                    </div>
                @elseif(!$isThursday)
                    <div class="bg-amber-500/5 border border-amber-500/20 rounded-2xl p-5 text-center">
                        <div class="text-amber-500/40 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <p class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em] mb-1">Vault Offline</p>
                        <p class="text-[9px] text-gray-500 font-medium leading-relaxed">
                            Payouts trigger every Thursday.<br>
                            Next: <span class="text-gray-400">{{ now()->next(Carbon\Carbon::THURSDAY)->format('M d') }}</span>
                        </p>
                    </div>
                @else
                    <form action="{{ route('redeem.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="relative">
                            <input type="text" name="phone_number" placeholder="GCASH/MAYA NUMBER" required
                                class="w-full bg-[#0d121d] border border-gray-800 rounded-xl py-4 px-4 text-xs text-white focus:border-cyan-500 outline-none transition-all placeholder:text-gray-700 font-mono tracking-widest text-center">
                        </div>
                        <button type="submit"
                            class="w-full bg-cyan-600 hover:bg-cyan-500 text-white font-black py-4 rounded-xl text-[10px] uppercase tracking-[0.2em] transition-all active:scale-95 shadow-lg shadow-cyan-900/40 {{ auth()->user()->current_balance <= 0 ? 'opacity-30 grayscale cursor-not-allowed' : '' }}"
                            {{ auth()->user()->current_balance <= 0 ? 'disabled' : '' }}>
                            Initialize Thursday Payout
                        </button>
                    </form>
                @endif
            </div>

            {{-- Listing Form --}}
            <div class="bg-[#151a26] border border-gray-800 rounded-3xl p-6 shadow-2xl" x-data="{ imageUrl: '' }">
                <h2 class="text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                    Create New Listing
                </h2>

                <form action="{{ route('products.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-4">
                        <input name="name" type="text" placeholder="PRODUCT NAME" required
                            class="w-full bg-[#0d121d] border border-gray-800 rounded-xl py-3 px-4 text-[11px] text-white focus:border-emerald-500 outline-none">

                        <div class="relative">
                            <input x-model="imageUrl" name="image_url" type="url" placeholder="THUMBNAIL URL" required
                                class="w-full bg-[#0d121d] border border-gray-800 rounded-xl py-3 px-4 text-[11px] text-white focus:border-emerald-500 outline-none">
                            <template x-if="imageUrl">
                                <img :src="imageUrl"
                                    class="absolute right-2 top-2 h-7 w-7 rounded-md object-cover border border-emerald-500/30">
                            </template>
                        </div>

                        <input name="price" type="number" step="0.01" placeholder="BASE PRICE (PHP)" required
                            class="w-full bg-[#0d121d] border border-gray-800 rounded-xl py-3 px-4 text-[11px] text-white focus:border-emerald-500 outline-none">
                    </div>

                    <button type="submit"
                        class="w-full bg-emerald-600/10 hover:bg-emerald-600 border border-emerald-500/20 hover:border-emerald-500 text-emerald-500 hover:text-white font-black py-3 rounded-xl text-[10px] uppercase tracking-widest transition-all">
                        Deploy to Marketplace
                    </button>
                </form>
            </div>

            {{-- Batch Ingress --}}
            <div class="bg-[#151a26]/60 border border-gray-800/50 rounded-3xl p-6 backdrop-blur-sm">
                <h2 class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-4 text-center">Batch Asset Ingress</h2>
                <form action="{{ route('stocks.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <select name="product_id" required
                        class="w-full bg-[#0d121d] border border-gray-800 rounded-xl py-3 px-4 text-[10px] text-gray-400 outline-none">
                        <option value="" disabled selected>TARGET UNIT...</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                    <textarea name="data" rows="2" placeholder="USER:PASS DATA" required
                        class="w-full bg-[#0d121d] border border-gray-800 rounded-xl py-3 px-4 text-[10px] text-white outline-none resize-none font-mono"></textarea>
                    <button type="submit"
                        class="w-full bg-gray-800 hover:bg-gray-700 text-gray-400 font-bold py-2 rounded-xl text-[9px] uppercase tracking-widest transition-all">
                        Append Stock
                    </button>
                </form>
            </div>

        </div>
    </div>
@endsection