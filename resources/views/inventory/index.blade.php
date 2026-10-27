@extends('layouts.app')
@section('title', 'Inventory')
@section('content')

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="card p-3">
            <h6>Produk Stok Menipis</h6>
            <ul class="small mb-0">
                @foreach($lowStock as $p)
                <li>{{ $p->name }} - Stok: {{ $p->stock }} (min: {{ $p->min_stock }})</li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3">
            <h6>Produk Habis</h6>
            <ul class="small mb-0">
                @foreach($emptyStock as $p)
                <li>{{ $p->name }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<div class="card p-3 mb-3">
    <h6>Penyesuaian Stok</h6>
    <form action="{{ route('inventory.adjust') }}" method="POST" class="row g-2">
        @csrf
        <div class="col-md-4">
            <select name="product_id" class="form-select form-select-sm" required>
                <option value="">- Pilih Produk -</option>
                @foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }} (stok: {{ $p->stock }})</option>@endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="type" class="form-select form-select-sm" required>
                <option value="adjustment">Penyesuaian</option>
                <option value="opname">Stok Opname</option>
            </select>
        </div>
        <div class="col-md-2"><input type="number" name="new_stock" class="form-control form-control-sm" placeholder="Stok baru" min="0" required></div>
        <div class="col-md-3"><input type="text" name="notes" class="form-control form-control-sm" placeholder="Keterangan"></div>
        <div class="col-md-1"><button class="btn btn-sm btn-primary w-100">Simpan</button></div>
    </form>
</div>

<div class="card p-3">
    <h6>Riwayat Pergerakan Stok</h6>
    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead class="table-light"><tr><th>Tanggal</th><th>Produk</th><th>Jenis</th><th>Qty</th><th>Sebelum</th><th>Sesudah</th><th>User</th><th>Ket</th></tr></thead>
            <tbody>
                @foreach($movements as $m)
                <tr>
                    <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $m->product->name ?? '-' }}</td>
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
    {{ $movements->links() }}
</div>
@endsection