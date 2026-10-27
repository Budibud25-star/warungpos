@extends('layouts.app')
@section('title', 'Laporan Stok')
@section('content')
<div class="card p-3">
    <h6>Semua Produk</h6>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead><tr><th>Kode</th><th>Nama</th><th>Kategori</th><th>Stok</th><th>Min</th></tr></thead>
            <tbody>
                @foreach($products as $p)
                <tr class="{{ $p->stock <= $p->min_stock ? 'table-danger' : '' }}">
                    <td>{{ $p->code }}</td><td>{{ $p->name }}</td>
                    <td>{{ $p->category->name ?? '-' }}</td>
                    <td>{{ $p->stock }}</td><td>{{ $p->min_stock }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection