@extends('layouts.app')
@section('title', 'Detail Pembelian')
@section('content')
<div class="card p-3">
    <h5>{{ $purchase->invoice_number }}</h5>
    <p>{{ $purchase->date->format('d/m/Y') }} - Supplier: {{ $purchase->supplier->name }} - User: {{ $purchase->user->name }}</p>
    <table class="table table-sm">
        <thead><tr><th>Produk</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead>
        <tbody>
            @foreach($purchase->items as $i)
            <tr><td>{{ $i->product->name }}</td><td>{{ $i->quantity }}</td><td>{{ format_rupiah($i->price) }}</td><td>{{ format_rupiah($i->subtotal) }}</td></tr>
            @endforeach
        </tbody>
    </table>
    <div class="text-end">
        <p>Subtotal: {{ format_rupiah($purchase->subtotal) }}</p>
        <p>Diskon: {{ format_rupiah($purchase->discount) }}</p>
        <h5>Total: {{ format_rupiah($purchase->grand_total) }}</h5>
        <p>Dibayar: {{ format_rupiah($purchase->paid_amount) }}</p>
        <p>Status: {{ ucfirst($purchase->payment_status) }}</p>
    </div>
</div>
@endsection