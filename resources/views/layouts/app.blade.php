<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Middle Guy') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-slate-50 antialiased">
    <div id="app">
        <div class="min-h-screen bg-[#0a0e18] text-white p-8 font-sans">
            <header class="flex justify-between items-center mb-10 border-b border-gray-800 pb-5">
                <div class="flex items-center gap-8">
                    <a href="{{ url('/') }}" class="group">
                        <h1
                            class="text-2xl font-bold tracking-wider uppercase text-cyan-400 group-hover:text-cyan-300 transition-colors">
                            Middle Guy
                        </h1>
                    </a>
                    @auth
                        <nav class="hidden md:flex items-center gap-4">
                            <a href="{{ route('purchases.index') }}"
                                class="text-sm font-medium text-gray-400 hover:text-cyan-400 transition-colors flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                My Purchases
                            </a>

                            <a href="{{ route('stocks.index') }}"
                                class="text-sm font-medium text-gray-400 hover:text-cyan-400 transition-colors flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Inventory
                            </a>
                        </nav>
                    @endauth
                </div>

                <div class="flex items-center gap-6">
                    @auth
                        <div class="flex items-center gap-4">
                            <div class="text-right hidden sm:block">
                                <p class="text-xs text-gray-500">Logged in as</p>
                                <p class="text-sm font-semibold text-gray-300">{{ auth()->user()->name }}</p>
                            </div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button
                                    class="bg-red-500/10 hover:bg-red-500/20 text-red-500 border border-red-500/50 px-3 py-1 rounded text-xs font-bold transition-all">
                                    LOGOUT
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login.google') }}"
                            class="bg-white text-black font-bold py-2 px-4 rounded-lg flex items-center gap-2 hover:bg-gray-200 transition-all shadow-[0_0_15px_rgba(255,255,255,0.1)]">
                            <img src="https://www.google.com/favicon.ico" class="w-4 h-4">
                            Login
                        </a>
                    @endauth
                </div>
            </header>

            <main>
                @yield('content')
            </main>
        </div>
        <!-- <chat-sidebar></chat-sidebar>
        <product-manager></product-manager> -->

    </div>
</body>

</html>