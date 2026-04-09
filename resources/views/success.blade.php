@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] px-4">
    <div class="mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30">
        <svg class="h-12 w-12 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </div>

    <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Payment Successful!</h1>
    <p class="text-slate-500 dark:text-slate-400 text-center max-w-md mb-8">
        Your transaction has been processed securely via GCash. Your account details or digital assets are now waiting for you.
    </p>

    <div class="flex flex-col sm:flex-row gap-4 w-full max-w-sm">
        <a href="/my-purchases" 
           class="flex-1 flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 transition-all hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            Go to Vault
        </a>
        
        <a href="/" 
           class="flex-1 flex items-center justify-center rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-6 py-3.5 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
            Continue Shopping
        </a>
    </div>

    <p class="mt-8 text-xs text-slate-400">
        Transaction ID: <span class="font-mono">{{ strtoupper(Str::random(12)) }}</span>
    </p>
</div>
@endsection