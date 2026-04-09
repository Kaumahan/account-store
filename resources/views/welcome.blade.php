@extends('layouts.app')

@section('content')
<div class="relative min-h-screen overflow-hidden bg-[#0f172a] py-12 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-20"></div>
        
        <div class="absolute -top-[10%] -left-[10%] h-[500px] w-[500px] rounded-full bg-indigo-500/10 blur-[120px]"></div>
        <div class="absolute top-[20%] -right-[10%] h-[400px] w-[400px] rounded-full bg-blue-600/10 blur-[100px]"></div>
        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[300px] w-full bg-gradient-to-t from-indigo-500/5 to-transparent blur-3xl"></div>
    </div>

    <div class="relative z-10 mx-auto max-w-7xl">
        
        <div class="mb-10 text-center">
            <h2 class="text-3xl font-black uppercase tracking-tighter text-white sm:text-4xl">
                Active <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">Listings</span>
            </h2>
            <div class="mt-2 flex justify-center gap-1">
                <div class="h-1 w-12 rounded-full bg-indigo-500"></div>
                <div class="h-1 w-2 rounded-full bg-indigo-500/40"></div>
            </div>
        </div>

        <div class="flex flex-wrap justify-center gap-8">
            @forelse($products as $product)
                {{-- 
                   Container with a fixed max-width for solo items, 
                   but responsive sizing for multiples. 
                --}}
                <div class="w-full max-w-[320px] transition-all duration-500 ease-out">
                    <product-card 
                        :product="{{ json_encode($product) }}"
                        :is-logged-in="{{ json_encode(auth()->check()) }}"
                    ></product-card>
                </div>
            @empty
                <div class="col-span-full flex min-h-[450px] w-full flex-col items-center justify-center rounded-3xl border border-slate-800 bg-slate-900/50 p-12 text-center backdrop-blur-xl">
                    <div class="relative mb-6">
                        <div class="absolute inset-0 animate-ping rounded-full bg-indigo-500/20"></div>
                        <div class="relative rounded-full bg-slate-800 p-6 text-indigo-400 shadow-2xl">
                            <svg class="h-14 w-14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-white uppercase tracking-tight">No Drops Found</h3>
                    <p class="mt-2 max-w-xs text-sm text-slate-400">The vault is empty. Be the first to list an account or check back later.</p>
                    
                    <a href="{{ url('/') }}" class="mt-8 rounded-xl bg-indigo-600 px-8 py-3 text-sm font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-500/25 transition-all hover:bg-indigo-500 hover:shadow-indigo-500/40 active:scale-95">
                        Reset Scanner
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection