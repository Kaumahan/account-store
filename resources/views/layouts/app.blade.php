<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Middle Guy') }}</title>

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

        .glass-header {
            background: rgba(10, 14, 24, 0.9);
            backdrop-filter: blur(12px);
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
                            </nav>
                        @endauth
                    </div>

                    <div class="flex items-center gap-4">
                        @auth
                            <div class="hidden md:flex items-center gap-3 pr-4 border-r border-slate-800">
                                <div class="text-right">
                                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Active
                                        Operative</p>
                                    <p class="text-sm font-black text-white">{{ explode(' ', auth()->user()->name)[0] }}</p>
                                </div>
                                <div
                                    class="h-10 w-10 rounded-full border-2 border-cyan-400/30 bg-slate-800 flex items-center justify-center text-cyan-400">
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

            <div x-show="mobileMenuOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                class="lg:hidden absolute top-20 left-0 w-full bg-[#0d1321] border-b border-slate-800 shadow-2xl">
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

    <script>
        lucide.createIcons();
    </script>
</body>

</html>