@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4" style="font-family: 'JetBrains Mono', monospace;">
    <div class="flex items-center justify-between mb-8 border-b border-cyan-900 pb-4">
        <h2 class="text-2xl font-extrabold text-cyan-400 uppercase tracking-[0.2em]">VAULT</h2>
        <span class="text-[10px] text-cyan-700 font-bold uppercase tracking-widest">
            System Status: <span class="text-green-500">Nominal</span>
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($payments as $payment)
            @if($payment->stock)
                <div class="relative p-[1px] rounded-sm bg-gradient-to-br from-cyan-900 to-transparent hover:from-cyan-500 transition-all duration-300">
                    <div class="bg-[#0b0f1a] p-1">
                        
                        <div class="absolute top-4 right-4 z-10">
                            @if($payment->hasActiveReport())
                                <div class="w-8 h-8 flex items-center justify-center text-yellow-500 bg-yellow-950/20 border border-yellow-900/50 rounded" title="Under Investigation">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                            @else
                                <button 
                                    type="button"
                                    onclick="window.dispatchEvent(new CustomEvent('open-report-modal', { detail: { id: {{ $payment->id }}, name: '{{ $payment->product->name }}' } }))"
                                    class="flex items-center justify-center w-8 h-8 rounded border border-red-900/50 bg-red-950/20 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-200 shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                                    </svg>
                                </button>
                            @endif
                        </div>

                        <product-credential-card 
                            :product='{!! json_encode($payment->product) !!}' 
                            :credentials='{!! json_encode([
                                "account_data" => $payment->stock->data,
                                "guide" => $payment->product->description ?? ""
                            ]) !!}'>
                        </product-credential-card>
                    </div>
                </div>
            @endif
        @empty
            <div class="col-span-full text-center py-20 bg-[#0d1117] border border-dashed border-gray-800">
                <p class="text-gray-600 uppercase font-bold text-sm tracking-[0.3em]">No Assets Found</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('modals')
    <div x-data="{ 
            show: false, 
            paymentId: null, 
            productName: '',
            reason: 'invalid_login',
            details: ''
         }"
         x-on:open-report-modal.window="show = true; paymentId = $event.detail.id; productName = $event.detail.name"
         x-show="show" 
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
         x-cloak>
        
        <div class="absolute inset-0 bg-black/90 backdrop-blur-md" @click="show = false"></div>

        <div class="relative bg-[#0b0f1a] border border-red-900/50 w-full max-w-md p-6 shadow-[0_0_50px_rgba(220,38,38,0.1)]"
             style="font-family: 'JetBrains Mono', monospace;"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <div class="flex justify-between items-center mb-6 border-b border-red-900/30 pb-3">
                <h3 class="text-red-500 font-black uppercase tracking-widest text-sm">
                    Report Incident
                </h3>
                <span class="text-[10px] text-slate-500 uppercase">Ref: #<span x-text="paymentId"></span></span>
            </div>

            <form :action="'/payments/' + paymentId + '/report'" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-cyan-600 text-[10px] uppercase font-bold mb-2 tracking-widest">Asset Name</label>
                        <div class="text-white text-sm bg-slate-900/50 p-2 border border-slate-800 rounded-sm" x-text="productName"></div>
                    </div>

                    <div>
                        <label class="block text-cyan-600 text-[10px] uppercase font-bold mb-2 tracking-widest">Issue Type</label>
                        <select name="reason" x-model="reason" required class="w-full bg-[#0d1117] border border-cyan-900/30 text-white text-sm p-3 outline-none focus:border-red-500 transition-all">
                            <option value="invalid_login">Invalid Credentials</option>
                            <option value="account_locked">Account Locked/Banned</option>
                            <option value="expired_sub">Subscription Expired</option>
                            <option value="other">Other Technical Fault</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-cyan-600 text-[10px] uppercase font-bold mb-2 tracking-widest">Incident Details</label>
                        <textarea name="details" x-model="details" rows="4" required class="w-full bg-[#0d1117] border border-cyan-900/30 text-white text-sm p-3 outline-none focus:border-red-500 transition-all" placeholder="Explain the error or paste logs here..."></textarea>
                    </div>

                    <div class="flex justify-between items-center pt-4">
                        <button type="button" @click="show = false" class="text-slate-500 text-[10px] uppercase font-bold hover:text-white transition-colors">Abort</button>
                        <button type="submit" class="bg-red-600/10 border border-red-600 text-red-500 px-6 py-2 text-[11px] uppercase font-black hover:bg-red-600 hover:text-white transition-all shadow-[0_0_15px_rgba(220,38,38,0.2)]">
                            Transmit Report
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endpush