@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#0a0e18] py-12 px-6 text-white font-sans">
    <div class="max-w-4xl mx-auto space-y-12">
        
        {{-- SECTION: PENDING PAYOUTS --}}
        <div>
            <h1 class="text-2xl font-black uppercase tracking-widest text-emerald-500 mb-6 flex items-center gap-3">
                <span class="w-2 h-2 bg-emerald-500 animate-pulse rounded-full"></span>
                Pending Payouts
            </h1>

            <div class="bg-[#151a26] border border-gray-800 rounded-3xl overflow-hidden shadow-2xl">
                <table class="w-full text-left">
                    <thead class="bg-[#0d121d]">
                        <tr class="text-[10px] font-black text-gray-500 uppercase">
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">Amount</th>
                            <th class="px-6 py-4">Phone (GCash/Maya)</th>
                            <th class="px-6 py-4">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/50">
                        @forelse($pendingPayouts as $payout)
                        <tr class="hover:bg-white/5 transition-all">
                            <td class="px-6 py-4 text-sm font-bold">{{ $payout->user->name }}</td>
                            <td class="px-6 py-4 text-emerald-400 font-mono">₱{{ number_format($payout->amount, 2) }}</td>
                            <td class="px-6 py-4 font-mono text-cyan-400">{{ $payout->phone_number }}</td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.approve', $payout->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-black uppercase px-4 py-2 rounded-lg transition-all shadow-lg shadow-emerald-900/20">
                                        Mark as Paid
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-10 text-center text-gray-500 italic text-sm">No pending claims. System clear.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <hr class="border-gray-800/50">

        {{-- SECTION: PAID HISTORY --}}
        <div>
            <h2 class="text-lg font-black uppercase tracking-widest text-gray-500 mb-6">Completed Payouts</h2>

            <div class="bg-[#151a26]/50 border border-gray-800/50 rounded-3xl overflow-hidden opacity-80">
                <table class="w-full text-left">
                    <thead class="bg-[#0d121d]/50">
                        <tr class="text-[9px] font-black text-gray-600 uppercase">
                            <th class="px-6 py-3">User</th>
                            <th class="px-6 py-3">Amount</th>
                            <th class="px-6 py-3">Processed At</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/30">
                        @forelse($completedPayouts as $paid)
                        <tr class="text-gray-400">
                            <td class="px-6 py-3 text-xs">{{ $paid->user->name }}</td>
                            <td class="px-6 py-3 font-mono text-xs">₱{{ number_format($paid->amount, 2) }}</td>
                            <td class="px-6 py-3 font-mono text-[10px] uppercase text-gray-600">
                                {{ $paid->updated_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-[9px] font-black uppercase px-2 py-1 bg-gray-800 text-gray-500 rounded border border-gray-700">
                                    Released
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-gray-600 text-xs italic">No history found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection