<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\User;
use App\Services\SaleService;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with('user', 'customer');
        if ($request->filled('search')) $query->where('invoice_number', 'like', '%' . $request->search . '%');
        if ($request->filled('date_from')) $query->whereDate('date', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('date', '<=', $request->date_to);
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('payment_method')) $query->where('payment_method', $request->payment_method);

        $sales = $query->orderByDesc('date')->paginate(20);
        $users = User::orderBy('name')->get();
        return view('sales.index', compact('sales', 'users'));
    }

    public function show(Sale $sale)
    {
        $sale->load('items.product', 'customer', 'user');
        return view('sales.show', compact('sale'));
    }

    public function void(Request $request, Sale $sale, SaleService $saleService)
    {
        $request->validate(['void_reason' => 'required|string']);
        try {
            $saleService->voidSale($sale, $request->void_reason, auth()->id());
            AuditLog::record("Void transaksi {$sale->invoice_number}", 'sale', $sale->id);
            return back()->with('success', 'Transaksi berhasil di-void.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}