<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\CashTransaction;
use App\Models\Product;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    private function range(Request $request)
    {
        $filter = $request->get('filter', 'today');
        $from = now()->startOfDay();
        $to = now()->endOfDay();

        switch ($filter) {
            case 'yesterday': $from = now()->subDay()->startOfDay(); $to = now()->subDay()->endOfDay(); break;
            case 'week': $from = now()->startOfWeek(); $to = now()->endOfWeek(); break;
            case 'month': $from = now()->startOfMonth(); $to = now()->endOfMonth(); break;
            case 'custom':
                $from = Carbon::parse($request->date_from)->startOfDay();
                $to = Carbon::parse($request->date_to)->endOfDay();
                break;
        }
        return [$from, $to];
    }

    public function sales(Request $request)
    {
        [$from, $to] = $this->range($request);
        $sales = Sale::with('user', 'customer')
            ->whereBetween('date', [$from, $to])
            ->where('status', 'completed')
            ->orderByDesc('date')->get();
        $total = $sales->sum('grand_total');
        return view('reports.sales', compact('sales', 'total', 'from', 'to'));
    }

    public function salesPdf(Request $request)
    {
        [$from, $to] = $this->range($request);
        $sales = Sale::with('user', 'customer')
            ->whereBetween('date', [$from, $to])->where('status', 'completed')->get();
        $total = $sales->sum('grand_total');
        $pdf = Pdf::loadView('reports.pdf.sales', compact('sales', 'total', 'from', 'to'));
        return $pdf->download('laporan-penjualan.pdf');
    }

    public function purchases(Request $request)
    {
        [$from, $to] = $this->range($request);
        $purchases = Purchase::with('supplier', 'user')
            ->whereBetween('date', [$from, $to])->orderByDesc('date')->get();
        $total = $purchases->sum('grand_total');
        return view('reports.purchases', compact('purchases', 'total', 'from', 'to'));
    }

    public function profit(Request $request)
    {
        [$from, $to] = $this->range($request);
        $items = SaleItem::with('product', 'sale')
            ->whereHas('sale', fn($q) => $q->whereBetween('date', [$from, $to])->where('status', 'completed'))
            ->get();

        $profit = $items->sum(function ($i) {
            return ($i->price - ($i->product->purchase_price ?? 0)) * $i->quantity;
        });
        $totalSales = $items->sum('subtotal');
        $totalModal = $items->sum(fn($i) => ($i->product->purchase_price ?? 0) * $i->quantity);

        return view('reports.profit', compact('items', 'profit', 'totalSales', 'totalModal', 'from', 'to'));
    }

    public function stock(Request $request)
    {
        $products = Product::with('category', 'supplier')->orderBy('stock')->get();
        $lowStock = $products->filter(fn($p) => $p->stock <= $p->min_stock && $p->stock > 0);
        $emptyStock = $products->where('stock', '<=', 0);
        return view('reports.stock', compact('products', 'lowStock', 'emptyStock'));
    }

    public function bestseller(Request $request)
    {
        [$from, $to] = $this->range($request);
        $best = SaleItem::select('product_id', DB::raw('SUM(quantity) as qty'), DB::raw('SUM(subtotal) as total'))
            ->whereHas('sale', fn($q) => $q->whereBetween('date', [$from, $to])->where('status', 'completed'))
            ->groupBy('product_id')->orderByDesc('qty')->with('product')->limit(50)->get();
        return view('reports.bestseller', compact('best', 'from', 'to'));
    }

    public function expense(Request $request)
    {
        [$from, $to] = $this->range($request);
        $expenses = Expense::with('category', 'user')
            ->whereBetween('date', [$from, $to])->orderByDesc('date')->get();
        $total = $expenses->sum('amount');
        return view('reports.expense', compact('expenses', 'total', 'from', 'to'));
    }

    public function cash(Request $request)
    {
        [$from, $to] = $this->range($request);
        $transactions = CashTransaction::with('user')
            ->whereBetween('date', [$from, $to])->orderByDesc('date')->get();
        $totalIn = $transactions->where('type', 'in')->sum('amount');
        $totalOut = $transactions->where('type', 'out')->sum('amount');
        $balance = $totalIn - $totalOut;
        return view('reports.cash', compact('transactions', 'totalIn', 'totalOut', 'balance', 'from', 'to'));
    }

    public function customer(Request $request)
    {
        $customers = Customer::withCount('sales')
            ->withSum(['sales' => fn($q) => $q->where('status', 'completed')], 'grand_total')
            ->orderByDesc('sales_sum_grand_total')->get();
        return view('reports.customer', compact('customers'));
    }
}