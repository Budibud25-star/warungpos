@extends('layouts.app')
@section('title', 'Produk Terlaris')
@section('content')
<div class="card p-3">
    <p>Periode: {{ $from->format('d/m/Y') }} - {{ $to->format('d/m/Y') }}</p>
    <table class="table table-sm">
        <thead><tr><th>#</th><th>Produk</th><th>Qty Terjual</th><th>Total Penjualan</th></tr></thead>
        <tbody>
            @foreach($best as $i => $b)
            <tr><td>{{ $i+1 }}</td><td>{{ $b->product->name ?? '-' }}</td><td>{{ $b->qty }}</td><td>{{ format_rupiah($b->total) }}</td></tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection