<?php

namespace App\Http\Controllers;

use App\Models\Redemption;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PayoutController extends Controller
{
    public function approve(Redemption $redemption)
    {
        // Only allow approving pending ones
        if ($redemption->status !== 'pending')
            return back();

        $redemption->update([
            'status' => 'completed',
            'processed_at' => now()
        ]);

        return back()->with('success', 'Payout confirmed.');
    }
}