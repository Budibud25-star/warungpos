@extends('layouts.app')
@section('title', 'Detail Produk')
@section('content')
<div class="row g-3">
    <div class="col-md-4">
        <div class="card p-3">
            @if($product->photo)<img src="{{ asset('storage/'.$product->photo) }}" class="img-fluid mb-2">@endif
            <h5>{{ $product->name }}</h5>
            <p class="text-muted small">Kode: {{ $product->code }} | Barcode: {{ $product->barcode ?? '-' }}</p>
            <table class="table table-sm">
                <tr><td>Kategori</td><td>{{ $product->category->name ?? '-' }}</td></tr>
                <tr><td>Supplier</td><td>{{ $product->supplier->name ?? '-' }}</td></tr>
                <tr><td>Satuan</td><td>{{ $product->unit }}</td></tr>
                <tr><td>Harga Beli</td><td>{{ format_rupiah($product->purchase_price) }}</td></tr>
                <tr><td>Harga Jual</td><td>{{ format_rupiah($product->selling_price) }}</td></tr>
                <tr><td>Stok</td><td><strong>{{ $product->stock }}</strong> (min: {{ $product->min_stock }})</td></tr>
                <tr><td>Status</td><td>{{ $product->status ? 'Aktif' : 'Nonaktif' }}</td></tr>
            </table>
            <p class="small">{{ $product->description }}</p>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card p-3">
            <h6>Riwayat Pergerakan Stok</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>Tanggal</th><th>Jenis</th><th>Qty</th><th>Sebelum</th><th>Sesudah</th><th>User</th><th>Ket</th></tr></thead>
                    <tbody>
                        @foreach($movements as $m)
                        <tr>
                            <td>{{ $m->created_at->format('d/m H:i') }}</td>
                            <td>{{ ucfirst($m->type) }}</td>
                            <td class="{{ $m->quantity < 0 ? 'text-danger' : 'text-success' }}">{{ $m->quantity }}</td>
                            <td>{{ $m->stock_before }}</td>
                            <td>{{ $m->stock_after }}</td>
                            <td>{{ $m->user->name ?? '-' }}</td>
                            <td>{{ $m->notes }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection