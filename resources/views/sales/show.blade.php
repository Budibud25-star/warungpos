@extends('layouts.app')
@section('title', 'Detail Transaksi')
@section('content')
<div class="row g-3">
    <div class="col-md-8">
        <div class="card p-3">
            <h5>Transaksi {{ $sale->invoice_number }}</h5>
            <p class="small text-muted">{{ $sale->date->format('d/m/Y H:i') }} - {{ $sale->user->name }}</p>
            <table class="table table-sm">
                <thead><tr><th>Produk</th><th>Qty</th><th>Harga</th><th>Diskon</th><th>Subtotal</th></tr></thead>
                <tbody>
                    @foreach($sale->items as $i)
                    <tr>
                        <td>{{ $i->product->name }}</td>
                        <td>{{ $i->quantity }}</td>
                        <td>{{ format_rupiah($i->price) }}</td>
                        <td>{{ format_rupiah($i->discount) }}</td>
                        <td>{{ format_rupiah($i->subtotal) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="text-end">
                <p>Subtotal: {{ format_rupiah($sale->subtotal) }}</p>
                <p>Diskon: {{ format_rupiah($sale->discount) }}</p>
                <p>Pajak: {{ format_rupiah($sale->tax) }}</p>
                <h5>Total: {{ format_rupiah($sale->grand_total) }}</h5>
                <p>Bayar: {{ format_rupiah($sale->paid_amount) }}</p>
                <p>Kembali: {{ format_rupiah($sale->change_amount) }}</p>
            </div>
            <a href="{{ route('pos.receipt', $sale) }}" target="_blank" class="btn btn-primary"><i class="bi bi-printer"></i> Cetak Struk</a>
            <a href="{{ route('pos.receipt.pdf', $sale) }}" class="btn btn-secondary"><i class="bi bi-file-pdf"></i> PDF</a>

            @if(in_array(auth()->user()->role->name, ['admin','owner']) && $sale->status === 'completed')
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#voidModal"><i class="bi bi-x-circle"></i> Void</button>
            @endif

            @if($sale->status === 'void')
            <div class="alert alert-danger mt-3">Transaksi di-void: {{ $sale->void_reason }}</div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="voidModal">
    <div class="modal-dialog"><form action="{{ route('sales.void', $sale) }}" method="POST" class="modal-content">
        @csrf
        <div class="modal-header"><h6>Void Transaksi</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <p>Alasan void:</p>
            <textarea name="void_reason" class="form-control" required></textarea>
        </div>
        <div class="modal-footer"><button class="btn btn-danger">Void</button></div>
    </form></div>
</div>
@endsection