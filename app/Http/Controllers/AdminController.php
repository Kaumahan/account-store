<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Models\Redemption;

class AdminController extends Controller
{
    public function payouts()
    {
        $pendingPayouts = Redemption::with('user')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $completedPayouts = Redemption::with('user')
            ->where('status', 'completed')
            ->latest()
            ->take(20)
            ->get();

        // FIXED: Remove the $ and wrap in quotes
        return view('admin.payouts', compact('pendingPayouts', 'completedPayouts'));
    }
    
    public function approve($id)
    {
        $redemption = Redemption::findOrFail($id);

        // Update status to completed
        $redemption->update([
            'status' => 'completed',
            'processed_at' => now()
        ]);

        return back()->with('success', 'Payout confirmed and archived.');
    }
}