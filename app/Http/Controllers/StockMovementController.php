<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with('product', 'user');
        if ($request->filled('product_id')) $query->where('product_id', $request->product_id);
        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);

        $movements = $query->orderByDesc('id')->paginate(30);
        $products = Product::orderBy('name')->get();
        $lowStock = Product::whereColumn('stock', '<=', 'min_stock')->get();
        $emptyStock = Product::where('stock', '<=', 0)->get();

        return view('inventory.index', compact('movements', 'products', 'lowStock', 'emptyStock'));
    }

    public function adjust(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:adjustment,opname',
            'new_stock' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($data) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);
            $before = $product->stock;
            $after = $data['new_stock'];
            $product->update(['stock' => $after]);

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => $data['type'],
                'quantity' => $after - $before,
                'stock_before' => $before,
                'stock_after' => $after,
                'notes' => $data['notes'] ?? 'Penyesuaian stok',
            ]);

            AuditLog::record("Penyesuaian stok {$product->name}: $before → $after", 'stock', $product->id);
        });

        return back()->with('success', 'Stok berhasil disesuaikan.');
    }
}