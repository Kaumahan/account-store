<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function create(Payment $payment)
    {
        // Security check: only the buyer can report
        if (auth()->id() !== $payment->user_id) {
            abort(403);
        }

        return view('reports.create', compact('payment'));
    }

    public function store(Request $request, Payment $payment)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'details' => 'nullable|string',
        ]);

        Report::create([
            'payment_id' => $payment->id,
            'user_id' => auth()->id(),
            'type' => $request->reason,
            'description' => $request->details,
        ]);


        return redirect()->route('purchases.index')->with('success', 'Incident report logged in system.');
    }
}