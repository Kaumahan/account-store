@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] px-4">
    <div class="mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-rose-100 dark:bg-rose-900/30">
        <svg class="h-12 w-12 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
        </svg>
    </div>

    <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Payment Failed</h1>
    <p class="text-slate-500 dark:text-slate-400 text-center max-w-md mb-8">
        We couldn't process your transaction. This might be due to insufficient funds, a network timeout, or a cancelled request.
    </p>

    <div class="flex flex-col sm:flex-row gap-4 w-full max-w-sm">
        <a href="javascript:history.back()" 
           class="flex-1 flex items-center justify-center gap-2 rounded-xl bg-slate-900 dark:bg-white dark:text-slate-900 px-6 py-3.5 text-sm font-semibold text-white shadow-lg hover:opacity-90 transition-all hover:-translate-y-0.5">
            Try Again
        </a>
        
        <a href="/" 
           class="flex-1 flex items-center justify-center rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-6 py-3.5 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
            Back to Shop
        </a>
    </div>

    <p class="mt-8 text-sm text-slate-500">
        Need help? <a href="/support" class="text-indigo-600 font-semibold hover:underline">Contact our support team</a>
    </p>
</div>
@endsection