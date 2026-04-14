@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-10 px-4" style="font-family: 'JetBrains Mono', monospace;">
    <h2 class="text-xl font-extrabold text-red-500 uppercase tracking-widest mb-6 border-b border-red-900 pb-2">
        FILE INCIDENT REPORT: #{{ $payment->id }}
    </h2>

    <form action="{{ route('reports.store', $payment->id) }}" method="POST" class="space-y-6">
        @csrf
        <div>
            <label class="block text-cyan-500 text-xs font-bold uppercase mb-2">Issue Category</label>
            <select name="reason" class="w-full bg-[#0b0f1a] border border-cyan-900 text-cyan-100 p-3 rounded-sm focus:border-cyan-500 outline-none">
                <option value="invalid_credentials">Invalid Credentials</option>
                <option value="account_locked">Account Locked</option>
                <option value="expired_premium">Expired Premium</option>
                <option value="other">Other Technical Issue</option>
            </select>
        </div>

        <div>
            <label class="block text-cyan-500 text-xs font-bold uppercase mb-2">Technical Details</label>
            <textarea name="details" rows="5" placeholder="Provide logs or error messages..." 
                class="w-full bg-[#0b0f1a] border border-cyan-900 text-cyan-100 p-3 rounded-sm focus:border-cyan-500 outline-none"></textarea>
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('purchases.index') }}" class="text-gray-500 text-xs hover:text-white uppercase">Cancel</a>
            <button type="submit" class="bg-red-900/20 border border-red-500 text-red-500 px-6 py-2 uppercase font-bold hover:bg-red-500 hover:text-white transition-all">
                Submit to Admin
            </button>
        </div>
    </form>
</div>
@endsection