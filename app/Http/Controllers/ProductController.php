<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category', 'supplier');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$s%")->orWhere('code', 'like', "%$s%"));
        }
        if ($request->filled('category_id')) $query->where('category_id', $request->category_id);
        if ($request->filled('stock_filter')) {
            if ($request->stock_filter === 'low') $query->whereColumn('stock', '<=', 'min_stock')->where('stock', '>', 0);
            elseif ($request->stock_filter === 'empty') $query->where('stock', '<=', 0);
        }
        $products = $query->orderByDesc('id')->paginate(20);
        $categories = Category::all();
        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|unique:products,code',
            'barcode' => 'nullable|unique:products,barcode',
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'unit' => 'required|string|max:20',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('products', 'public');
        }
        $data['status'] = $request->boolean('status', true);

        $product = Product::create($data);
        AuditLog::record("Menambah produk {$product->name}", 'product', $product->id);
        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load('category', 'supplier');
        $movements = $product->stockMovements()->with('user')->orderByDesc('id')->limit(50)->get();
        return view('products.show', compact('product', 'movements'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'code' => 'required|unique:products,code,' . $product->id,
            'barcode' => 'nullable|unique:products,barcode,' . $product->id,
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'unit' => 'required|string|max:20',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($product->photo) Storage::disk('public')->delete($product->photo);
            $data['photo'] = $request->file('photo')->store('products', 'public');
        }
        $data['status'] = $request->boolean('status', true);

        $product->update($data);
        AuditLog::record("Mengubah produk {$product->name}", 'product', $product->id);
        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->delete();
        AuditLog::record("Menghapus produk {$name}", 'product', $product->id);
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function export()
    {
        $products = Product::with('category')->get();
        $filename = 'produk-' . date('Ymd') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ];
        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Kode', 'Nama', 'Kategori', 'Satuan', 'Harga Beli', 'Harga Jual', 'Stok']);
            foreach ($products as $p) {
                fputcsv($file, [
                    $p->code, $p->name, $p->category->name ?? '-', $p->unit,
                    $p->purchase_price, $p->selling_price, $p->stock,
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}