<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Product;
use App\Services\PurchaseService;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('supplier', 'user');
        if ($request->filled('search')) $query->where('invoice_number', 'like', '%' . $request->search . '%');
        if ($request->filled('supplier_id')) $query->where('supplier_id', $request->supplier_id);
        $purchases = $query->orderByDesc('date')->paginate(20);
        $suppliers = Supplier::all();
        return view('purchases.index', compact('purchases', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 1)->get();
        $products = Product::where('status', 1)->get();
        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request, PurchaseService $purchaseService)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            $p = $purchaseService->createPurchase($request->all(), auth()->id());
            AuditLog::record("Menambah pembelian {$p->invoice_number}", 'purchase', $p->id);
            return redirect()->route('purchases.show', $p)->with('success', 'Pembelian berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('items.product', 'supplier', 'user');
        return view('purchases.show', compact('purchase'));
    }
}