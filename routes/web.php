<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;

Route::get('/', fn() => redirect()->route('login'));

Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil & Ubah Password (Semua Role)
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.update-password');

    // Kasir + Admin
    Route::middleware(['role:admin,kasir'])->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::get('/pos/products/search', [PosController::class, 'searchProducts'])->name('pos.products.search');
        Route::post('/pos/sales', [PosController::class, 'store'])->name('pos.sales.store');
    });

    // Riwayat (semua role)
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('/pos/receipt/{sale}', [PosController::class, 'receipt'])->name('pos.receipt');
    Route::get('/pos/receipt/{sale}/pdf', [PosController::class, 'receiptPdf'])->name('pos.receipt.pdf');

    // Void (admin, owner)
    Route::middleware(['role:admin,owner'])->group(function () {
        Route::post('/sales/{sale}/void', [SaleController::class, 'void'])->name('sales.void');
    });

    // Produk (admin, owner full; kasir lihat)
    Route::middleware(['role:admin,owner'])->group(function () {
        Route::resource('products', ProductController::class);
        Route::get('/products-export/csv', [ProductController::class, 'export'])->name('products.export');
        Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);
        Route::resource('suppliers', SupplierController::class)->except(['create', 'show', 'edit']);
        Route::resource('customers', CustomerController::class)->except(['create', 'edit']);
    });

    // Pembelian & Inventory
    Route::middleware(['role:admin,owner'])->group(function () {
        Route::resource('purchases', PurchaseController::class)->except(['edit', 'update', 'destroy']);
        Route::get('/inventory', [StockMovementController::class, 'index'])->name('inventory.index');
        Route::post('/inventory/adjust', [StockMovementController::class, 'adjust'])->name('inventory.adjust');
    });

    // Pengeluaran & Kas
    Route::middleware(['role:admin,owner'])->group(function () {
        Route::resource('expenses', ExpenseController::class)->only(['index', 'store', 'destroy']);
        Route::get('/cash', [CashController::class, 'index'])->name('cash.index');
        Route::post('/cash', [CashController::class, 'store'])->name('cash.store');
    });

    // Laporan
    Route::middleware(['role:admin,owner'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/sales/pdf', [ReportController::class, 'salesPdf'])->name('sales.pdf');
        Route::get('/purchases', [ReportController::class, 'purchases'])->name('purchases');
        Route::get('/profit', [ReportController::class, 'profit'])->name('profit');
        Route::get('/stock', [ReportController::class, 'stock'])->name('stock');
        Route::get('/bestseller', [ReportController::class, 'bestseller'])->name('bestseller');
        Route::get('/expense', [ReportController::class, 'expense'])->name('expense');
        Route::get('/cash', [ReportController::class, 'cash'])->name('cash');
        Route::get('/customer', [ReportController::class, 'customer'])->name('customer');
    });

    // Pengaturan
    Route::middleware(['role:admin,owner'])->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // User management - admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
    });
});