<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderByDesc('id')->paginate(20);
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);
        $data['status'] = $request->boolean('status', true);
        $c = Category::create($data);
        AuditLog::record("Menambah kategori {$c->name}", 'category', $c->id);
        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);
        $data['status'] = $request->boolean('status', true);
        $category->update($data);
        AuditLog::record("Mengubah kategori {$category->name}", 'category', $category->id);
        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Kategori masih memiliki produk.');
        }
        $name = $category->name;
        $category->delete();
        AuditLog::record("Menghapus kategori {$name}", 'category', $category->id);
        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}