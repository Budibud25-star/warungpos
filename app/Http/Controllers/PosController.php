<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Sale;
use App\Services\SaleService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index()
    {
        $categories = Category::where('status', 1)->get();
        $customers = Customer::orderBy('name')->get();
        return view('pos.index', compact('categories', 'customers'));
    }

    public function searchProducts(Request $request)
    {
        $query = Product::where('status', 1)->with('category');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('code', 'like', "%$s%")
                  ->orWhere('barcode', 'like', "%$s%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        return response()->json($query->limit(50)->get());
    }

    public function store(Request $request, SaleService $saleService)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,transfer,qris,debit,kredit,ewallet',
            'paid_amount' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
            'discount' => 'nullable|numeric|min:0',
        ]);

        try {
            $sale = $saleService->createSale($request->all(), auth()->id());
            \App\Models\AuditLog::record("Transaksi penjualan {$sale->invoice_number}", 'sale', $sale->id);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function receipt(Sale $sale)
    {
        $sale->load('items.product', 'customer', 'user');
        return view('pos.receipt', compact('sale'));
    }

    public function receiptPdf(Sale $sale)
    {
        $sale->load('items.product', 'customer', 'user');
        $pdf = Pdf::loadView('pos.receipt_pdf', compact('sale'))
            ->setPaper([0, 0, 226.77, 800], 'portrait');
        return $pdf->download("struk-{$sale->invoice_number}.pdf");
    }
}