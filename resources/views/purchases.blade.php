@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700;800&display=swap" rel="stylesheet">

<div class="max-w-7xl mx-auto py-10 px-4" style="font-family: 'JetBrains Mono', monospace;">
    <div class="flex items-center justify-between mb-8 border-b border-cyan-900 pb-4">
        <h2 class="text-2xl font-extrabold text-cyan-400 uppercase tracking-[0.2em]">
           VAULT
        </h2>
        <span class="text-[10px] text-cyan-700 font-bold uppercase tracking-widest">
            System Status: Nominal
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($payments as $payment)
            @if($payment->stock)
                <div class="p-[1px] rounded-sm bg-gradient-to-br from-cyan-900 to-transparent hover:from-cyan-500 transition-all duration-300">
                    <div class="bg-[#0b0f1a] p-1">
                        <product-credential-card 
                            :product='{!! json_encode($payment->product) !!}' 
                            :credentials='{!! json_encode([
                                "account_data" => $payment->stock->data,
                                "guide" => $payment->product->description
                            ]) !!}'>
                        </product-credential-card>
                    </div>
                </div>
            @endif
        @empty
            <div class="col-span-full text-center py-20 bg-[#0d1117] border-2 border-dashed border-gray-800 rounded-lg">
                <p class="text-gray-600 uppercase font-bold text-sm tracking-[0.3em]">
                     No Assets
                </p>
            </div>
        @endforelse
    </div>
</div>
@endsection