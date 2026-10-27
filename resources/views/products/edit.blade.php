@extends('layouts.app')
@section('title', 'Edit Produk')
@section('content')

<div class="card p-3">
    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Kode Produk *</label>
                <input type="text" name="code" class="form-control" value="{{ old('code', $product->code) }}" required>
            </div>
            <div class="col-md-4"><label class="form-label">Barcode</label>
                <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $product->barcode) }}">
            </div>
            <div class="col-md-4"><label class="form-label">Nama Produk *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
            </div>
            <div class="col-md-4"><label class="form-label">Kategori</label>
                <select name="category_id" class="form-select">
                    <option value="">- Pilih -</option>
                    @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected($product->category_id==$c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4"><label class="form-label">Supplier</label>
                <select name="supplier_id" class="form-select">
                    <option value="">- Pilih -</option>
                    @foreach($suppliers as $s)
                    <option value="{{ $s->id }}" @selected($product->supplier_id==$s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4"><label class="form-label">Satuan *</label>
                <input type="text" name="unit" class="form-control" value="{{ old('unit', $product->unit) }}" required>
            </div>
            <div class="col-md-4"><label class="form-label">Harga Beli</label>
                <input type="number" name="purchase_price" class="form-control" value="{{ old('purchase_price', $product->purchase_price) }}" min="0" required>
            </div>
            <div class="col-md-4"><label class="form-label">Harga Jual</label>
                <input type="number" name="selling_price" class="form-control" value="{{ old('selling_price', $product->selling_price) }}" min="0" required>
            </div>
            <div class="col-md-4"><label class="form-label">Stok</label>
                <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock) }}" min="0" required>
            </div>
            <div class="col-md-4"><label class="form-label">Stok Minimum</label>
                <input type="number" name="min_stock" class="form-control" value="{{ old('min_stock', $product->min_stock) }}" min="0" required>
            </div>
            <div class="col-md-8"><label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control">{{ old('description', $product->description) }}</textarea>
            </div>
            <div class="col-md-4"><label class="form-label">Foto</label>
                <input type="file" name="photo" class="form-control" accept="image/*">
                @if($product->photo)<img src="{{ asset('storage/'.$product->photo) }}" class="mt-2" style="max-height:80px">@endif
            </div>
            <div class="col-md-4"><label class="form-label">Status</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="status" value="1" @checked($product->status)>
                    <label class="form-check-label">Aktif</label>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <button class="btn btn-primary">Update</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection