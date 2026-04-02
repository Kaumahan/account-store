<body class="bg-slate-50 antialiased">
      <head>
       <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <div id="app">
  
      <div class="min-h-screen bg-[#0a0e18] text-white p-8 font-sans">
    <header class="flex justify-between items-center mb-10 border-b border-gray-800 pb-5">
    <h1 class="text-2xl font-bold tracking-wider uppercase text-cyan-400">Middle Guy</h1>
    <div class="flex items-center gap-6">
        @auth
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-400">{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="text-xs text-red-500 hover:underline">Logout</button>
                </form>
            </div>
        @else
            <a href="{{ route('login.google') }}" 
               class="bg-white text-black font-bold py-2 px-4 rounded-lg flex items-center gap-2 hover:bg-gray-200 transition-all">
               <img src="https://www.google.com/favicon.ico" class="w-4 h-4">
               Login with Google
            </a>
        @endauth
    </div>
</header>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($products as $product)
        <div class="group bg-[#151a26] border border-gray-800 rounded-xl overflow-hidden hover:border-cyan-500 transition-all duration-300 transform hover:-translate-y-2 hover:shadow-[0_0_20px_rgba(34,211,238,0.2)]">
            
            <div class="relative bg-[#1c2331] p-4 aspect-square flex items-center justify-center">
                <img src="{{ $product->image_url ?? 'https://via.placeholder.com/200' }}" alt="{{ $product->name }}" class="w-full h-full object-contain">
                <span class="absolute top-2 left-2 bg-black/50 backdrop-blur-md text-[10px] px-2 py-1 rounded border border-gray-600">
                    ID #{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}
                </span>
            </div>

            <div class="p-4 border-t border-gray-800">
                <h3 class="font-bold text-lg mb-1 truncate group-hover:text-cyan-400 transition-colors">
                    {{ $product->name }}
                </h3>
                
                <div class="flex items-center justify-between mt-4">
                    <div class="flex flex-col">
                        <span class="text-xs text-gray-400 uppercase tracking-tighter">Price</span>
                        <span class="text-xl font-black text-white">
                            ₱{{ number_format($product->price, 2) }}
                        </span>
                    </div>

                    <form action="{{ route('checkout', $product->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-cyan-600 hover:bg-cyan-500 text-white font-bold py-2 px-4 rounded-lg shadow-lg transition-all active:scale-95">
                            Buy Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
    <chat-sidebar></chat-sidebar>
    </div>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</body>