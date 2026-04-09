<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Redemption;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RedemptionController extends Controller
{
    public function store(Request $request)
    {
        // 1. Time-gate Check
        if (!now()->isThursday()) {
            return back()->with('error', 'The payout vault only opens on Thursdays.');
        }

        // 2. Validation (Added regex for PH phone numbers)
        $request->validate([
            'phone_number' => ['required', 'string', 'regex:/^(09|\+639)\d{9}$/'],
        ]);

        try {
            // 3. Database Transaction with Row Locking
            DB::transaction(function () use ($request) {
                /** * CRITICAL: Using lockForUpdate() prevents other requests from 
                 * reading this user's balance until this transaction finishes.
                 */
                $user = auth()->user()->fresh()->lockForUpdate();

                // 4. State Verification inside the lock
                if ($user->current_balance <= 0) {
                    throw new \Exception('Insufficient funds.');
                }

                if ($user->redemptions()->where('status', 'pending')->exists()) {
                    throw new \Exception('You already have a transfer in progress.');
                }

                // 5. Atomic Action
                $user->redemptions()->create([
                    'amount' => $user->current_balance,
                    'phone_number' => $request->phone_number,
                    'status' => 'pending',
                    'claimed_at' => now(),
                ]);

                // Update balance to zero
                $user->current_balance = 0;
                $user->save();
            });

            return back()->with('success', 'Payout initiated. Check back for approval.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}