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

        <div class="mt-24">
            <div class="relative overflow-hidden rounded-3xl border border-indigo-500/20 bg-slate-900/40 p-8 backdrop-blur-sm">
                <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-cyan-500/10 blur-3xl"></div>
                
                <div class="relative z-10 flex flex-col items-center justify-between gap-6 md:flex-row">
                    <div class="flex-1 text-center md:text-left">
                        <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-indigo-500/10 px-4 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-indigo-400">
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-indigo-400 opacity-75"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-indigo-500"></span>
                            </span>
                            Support a Cause
                        </div>
                        <h3 class="text-2xl font-black uppercase tracking-tight text-white">
                            Giving <span class="text-indigo-400">Strays</span> a Second Chance
                        </h3>
                        <p class="mt-2 max-w-xl text-sm leading-relaxed text-slate-400">
                            Do you have dogs or pets in need of a forever home? Join the registry to list strays or pets for adoption and help connect them with loving families.
                        </p>
                    </div>
                    
                    <div class="shrink-0">
                        <a href="https://animaladoptionandregistry.site/" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="group flex items-center gap-3 rounded-xl bg-white px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-900 transition-all hover:bg-indigo-400 hover:text-white hover:shadow-[0_0_25px_rgba(129,140,248,0.5)] active:scale-95">
                            List for Adoption
                            <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection