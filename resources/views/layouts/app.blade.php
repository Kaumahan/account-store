<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Middle Guy') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0a0e18;
        }

        /* Updated to be solid, not transparent */
        .glass-header {
            background: #0a0e18; 
            border-bottom: 1px solid rgba(51, 65, 85, 0.5);
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="antialiased text-slate-200">
    <div x-data="{ mobileMenuOpen: false }" @keydown.escape="mobileMenuOpen = false">

        <header class="sticky top-0 z-[100] glass-header">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">

                    <div class="flex items-center gap-8">
                        <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                            <div
                                class="h-9 w-9 bg-gradient-to-br from-cyan-400 to-indigo-600 rounded-lg shadow-[0_0_15px_rgba(34,211,238,0.3)] flex items-center justify-center">
                                <i data-lucide="shield-check" class="text-white w-5 h-5"></i>
                            </div>
                            <h1 class="text-xl font-black uppercase italic text-white tracking-tighter">
                                Middle<span class="text-cyan-400">Guy</span>
                            </h1>
                        </a>

                        @auth
                            <nav class="hidden lg:flex items-center gap-2">
                                <a href="{{ url('/') }}"
                                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-[11px] font-bold uppercase tracking-widest transition-all {{ request()->is('/') ? 'bg-cyan-400/10 text-cyan-400' : 'text-slate-400 hover:text-white' }}">
                                    <i data-lucide="layout-grid" class="w-3.5 h-3.5"></i> Shop
                                </a>
                                <a href="{{ route('purchases.index') }}"
                                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-[11px] font-bold uppercase tracking-widest transition-all {{ request()->routeIs('purchases.index') ? 'bg-cyan-400/10 text-cyan-400' : 'text-slate-400 hover:text-white' }}">
                                    <i data-lucide="package" class="w-3.5 h-3.5"></i> Vault
                                </a>
                                <a href="{{ route('stocks.index') }}"
                                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-[11px] font-bold uppercase tracking-widest transition-all {{ request()->routeIs('stocks.index') ? 'bg-cyan-400/10 text-cyan-400' : 'text-slate-400 hover:text-white' }}">
                                    <i data-lucide="plus-square" class="w-3.5 h-3.5"></i> Add New Listing
                                </a>

                                {{-- Admin Payout Tab --}}
                                @if(auth()->user()->is_admin)
                                <a href="{{ url('admin/payout') }}"
                                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-[11px] font-bold uppercase tracking-widest transition-all {{ request()->is('admin/payout*') ? 'bg-amber-400/10 text-amber-400' : 'text-slate-400 hover:text-white' }}">
                                    <i data-lucide="wallet" class="w-3.5 h-3.5"></i> Admin Payout
                                </a>
                                @endif
                            </nav>
                        @endauth
                    </div>

                    <div class="flex items-center gap-4">
                        @auth
                            <div class="hidden md:flex items-center gap-3 pr-4 border-r border-slate-800">
                                <div class="text-right">
                                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">
                                        {{ auth()->user()->is_admin ? 'Command Center' : 'Active Operative' }}
                                    </p>
                                    <p class="text-sm font-black text-white">{{ explode(' ', auth()->user()->name)[0] }}</p>
                                </div>
                                <div
                                    class="h-10 w-10 rounded-full border-2 {{ auth()->user()->is_admin ? 'border-amber-400/40' : 'border-cyan-400/30' }} bg-slate-800 flex items-center justify-center {{ auth()->user()->is_admin ? 'text-amber-400' : 'text-cyan-400' }}">
                                    <i data-lucide="user" class="w-5 h-5"></i>
                                </div>
                            </div>

                            <form action="{{ route('logout') }}" method="POST" class="hidden md:block">
                                @csrf
                                <button type="submit" class="p-2 text-slate-500 hover:text-red-400 transition-colors"
                                    title="Disconnect Session">
                                    <i data-lucide="log-out" class="w-5 h-5"></i>
                                </button>
                            </form>

                            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button"
                                class="lg:hidden p-2 text-cyan-400 focus:outline-none">
                                <i x-show="!mobileMenuOpen" data-lucide="menu" class="w-7 h-7"></i>
                                <i x-show="mobileMenuOpen" x-cloak data-lucide="x" class="w-7 h-7"></i>
                            </button>
                        @else
                            <a href="{{ route('login.google') }}"
                                class="bg-white text-black font-black py-2.5 px-6 rounded-xl flex items-center gap-3 hover:scale-105 transition-all shadow-[0_0_20px_rgba(255,255,255,0.15)] text-[11px] uppercase tracking-tighter">
                                Sign In
                                <img src="https://www.google.com/favicon.ico" class="w-4 h-4">
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            {{-- Mobile Menu --}}
            <div x-show="mobileMenuOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                class="lg:hidden absolute top-20 left-0 w-full bg-[#0a0e18] border-b border-slate-800 shadow-2xl">
                <div class="p-6 space-y-4">
                    @auth
                        <a href="{{ url('/') }}"
                            class="flex items-center gap-3 p-4 rounded-xl font-bold uppercase tracking-widest text-xs {{ request()->is('/') ? 'bg-cyan-400/10 text-cyan-400 border border-cyan-400/20' : 'bg-slate-900 text-slate-400' }}">
                            <i data-lucide="layout-grid" class="w-4 h-4"></i> Shop
                        </a>
                        <a href="{{ route('purchases.index') }}"
                            class="flex items-center gap-3 p-4 rounded-xl font-bold uppercase tracking-widest text-xs {{ request()->routeIs('purchases.index') ? 'bg-cyan-400/10 text-cyan-400 border border-cyan-400/20' : 'bg-slate-900 text-slate-400' }}">
                            <i data-lucide="package" class="w-4 h-4"></i> Vault
                        </a>
                        <a href="{{ route('stocks.index') }}"
                            class="flex items-center gap-3 p-4 rounded-xl font-bold uppercase tracking-widest text-xs {{ request()->routeIs('stocks.index') ? 'bg-cyan-400/10 text-cyan-400 border border-cyan-400/20' : 'bg-slate-900 text-slate-400' }}">
                            <i data-lucide="plus-square" class="w-4 h-4"></i> Add New Listing
                        </a>

                        @if(auth()->user()->is_admin)
                        <a href="{{ url('admin/payouts') }}"
                            class="flex items-center gap-3 p-4 rounded-xl font-bold uppercase tracking-widest text-xs {{ request()->is('admin/payout*') ? 'bg-amber-400/10 text-amber-400 border border-amber-400/20' : 'bg-slate-900 text-slate-400' }}">
                            <i data-lucide="wallet" class="w-4 h-4"></i> Admin Payout
                        </a>
                        @endif

                        <form action="{{ route('logout') }}" method="POST" class="pt-4 border-t border-slate-800">
                            @csrf
                            <button
                                class="flex items-center justify-center gap-2 w-full p-4 rounded-xl bg-red-500/10 text-red-500 font-black uppercase text-[10px] tracking-[0.2em]">
                                <i data-lucide="power" class="w-4 h-4"></i> Disconnect
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </header>

        <div id="app">
            <main class="py-8">
                @yield('content')
            </main>
        </div>
    </div>
    @stack('modals')
    <script>
        lucide.createIcons();
    </script>
</body>

</html>