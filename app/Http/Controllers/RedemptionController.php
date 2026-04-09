<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Redemption;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RedemptionController extends Controller
{
    public function store(Request $request)
    {
        // 1. Strict Security: Only allow requests on Thursdays
        if (!now()->isThursday()) {
            return back()->with('error', 'The payout vault only opens on Thursdays.');
        }

        $user = auth()->user();

        // 2. Validate Input
        $request->validate([
            'phone_number' => 'required|string|min:10|max:11',
        ]);

        // 3. Prevent logic errors (0 balance or duplicate claims)
        if ($user->current_balance <= 0) {
            return back()->with('error', 'Insufficient funds.');
        }

        if ($user->redemptions()->where('status', 'pending')->exists()) {
            return back()->with('error', 'You already have a transfer in progress.');
        }

        // 4. Atomic Transaction: Create claim and reset balance
        DB::transaction(function () use ($user, $request) {
            $user->redemptions()->create([
                'amount' => $user->current_balance, // Keeping whole amount (no division)
                'phone_number' => $request->phone_number,
                'status' => 'pending',
                'claimed_at' => now(),
            ]);

            $user->update(['current_balance' => 0]);
        });

        return back()->with('success', 'Payout initiated. Check back for approval.');
    }
}