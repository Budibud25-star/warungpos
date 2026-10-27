@extends('layouts.app')
@section('title', 'Produk')
@section('content')

<div class="card p-3">
    <div class="d-flex flex-wrap justify-content-between mb-3 gap-2">
        <form class="d-flex gap-2 flex-wrap" method="GET">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari produk..." value="{{ request('search') }}" style="width:200px">
            <select name="category_id" class="form-select form-select-sm" style="width:180px">
                <option value="">Semua Kategori</option>
                @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(request('category_id')==$c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            <select name="stock_filter" class="form-select form-select-sm" style="width:150px">
                <option value="">Semua Stok</option>
                <option value="low" @selected(request('stock_filter')=='low')>Stok Menipis</option>
                <option value="empty" @selected(request('stock_filter')=='empty')>Habis</option>
            </select>
            <button class="btn btn-sm btn-primary">Filter</button>
        </form>
        <div>
            <a href="{{ route('products.create') }}" class="btn btn-sm btn-success"><i class="bi bi-plus"></i> Tambah</a>
            <a href="{{ route('products.export') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-download"></i> Export</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr><th>Kode</th><th>Nama</th><th>Kategori</th><th>Harga Beli</th><th>Harga Jual</th><th>Stok</th><th>Status</th><th width="120">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                <tr>
                    <td>{{ $p->code }}</td>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->category->name ?? '-' }}</td>
                    <td>{{ format_rupiah($p->purchase_price) }}</td>
                    <td>{{ format_rupiah($p->selling_price) }}</td>
                    <td>
                        <span class="badge {{ $p->stock <= $p->min_stock ? 'bg-danger' : 'bg-success' }}">{{ $p->stock }}</span>
                    </td>
                    <td>{!! $p->status ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>' !!}</td>
                    <td>
                        <a href="{{ route('products.show', $p) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('products.edit', $p) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('products.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirmDelete(event)">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $products->withQueryString()->links() }}
</div>

@push('scripts')
<script>
function confirmDelete(e) {
    e.preventDefault();
    const form = e.target;
    Swal.fire({title:'Yakin hapus?', text:'Data akan dihapus', icon:'warning', showCancelButton:true, confirmButtonText:'Ya, hapus', cancelButtonText:'Batal'})
        .then(r => { if (r.isConfirmed) form.submit(); });
    return false;
}
</script>
@endpush
@endsection