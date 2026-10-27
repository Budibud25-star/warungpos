<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\CashTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();

        $totalSalesToday = Sale::whereDate('date', $today)->where('status', 'completed')->sum('grand_total');
        $totalTrxToday = Sale::whereDate('date', $today)->where('status', 'completed')->count();

        $profitToday = SaleItem::whereHas('sale', function ($q) use ($today) {
            $q->whereDate('date', $today)->where('status', 'completed');
        })->get()->sum(function ($item) {
            return ($item->price - $item->product->purchase_price) * $item->quantity;
        });

        $totalProducts = Product::count();
        $lowStock = Product::whereColumn('stock', '<=', 'min_stock')->count();

        $monthStart = now()->startOfMonth();
        $totalPurchaseMonth = Purchase::where('date', '>=', $monthStart)->sum('grand_total');
        $totalExpenseMonth = Expense::where('date', '>=', $monthStart)->sum('amount');
        $cashBalance = CashTransaction::balance();

        // Chart 7 hari
        $chart7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i)->toDateString();
            $chart7Days[] = [
                'date' => $d,
                'total' => (float) Sale::whereDate('date', $d)->where('status', 'completed')->sum('grand_total'),
            ];
        }

        // Chart 30 hari
        $chart30Days = Sale::where('date', '>=', now()->subDays(29))
            ->where('status', 'completed')
            ->select(DB::raw('DATE(date) as date'), DB::raw('SUM(grand_total) as total'))
            ->groupBy('date')->orderBy('date')
            ->get()->toArray();

        // Best seller
        $bestSellers = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('sale', fn($q) => $q->where('status', 'completed'))
            ->groupBy('product_id')->orderByDesc('total_qty')->limit(10)
            ->with('product')->get();

        // Penjualan per kategori
        $salesByCategory = DB::table('sale_items')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.status', 'completed')
            ->select('categories.name', DB::raw('SUM(sale_items.subtotal) as total'))
            ->groupBy('categories.name')->get();

        $recentSales = Sale::with('user', 'customer')
            ->orderByDesc('date')->limit(10)->get();

        return view('dashboard.index', compact(
            'totalSalesToday', 'totalTrxToday', 'profitToday',
            'totalProducts', 'lowStock', 'totalPurchaseMonth',
            'totalExpenseMonth', 'cashBalance',
            'chart7Days', 'chart30Days', 'bestSellers',
            'salesByCategory', 'recentSales'
        ));
    }
}