<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class CashController extends Controller
{
    public function index(Request $request)
    {
        $query = CashTransaction::with('user');
        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('date_from')) $query->whereDate('date', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('date', '<=', $request->date_to);

        $transactions = $query->orderByDesc('date')->orderByDesc('id')->paginate(30);
        $totalIn = CashTransaction::where('type', 'in')->sum('amount');
        $totalOut = CashTransaction::where('type', 'out')->sum('amount');
        $balance = $totalIn - $totalOut;

        return view('cash.index', compact('transactions', 'totalIn', 'totalOut', 'balance'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'date' => 'required|date',
        ]);
        $data['user_id'] = auth()->id();
        $data['reference_type'] = 'manual';
        CashTransaction::create($data);
        AuditLog::record("Kas {$data['type']} manual: {$data['amount']}", 'cash');
        return back()->with('success', 'Transaksi kas berhasil dicatat.');
    }
}