@extends('layouts.app')
@section('title', 'Pembelian')
@section('content')
<div class="card p-3">
    <div class="d-flex justify-content-between mb-3">
        <h6>Daftar Pembelian</h6>
        <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-success"><i class="bi bi-plus"></i> Tambah</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light"><tr><th>No</th><th>Tanggal</th><th>Supplier</th><th>Total</th><th>Bayar</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($purchases as $p)
                <tr>
                    <td>{{ $p->invoice_number }}</td>
                    <td>{{ $p->date->format('d/m/Y') }}</td>
                    <td>{{ $p->supplier->name }}</td>
                    <td>{{ format_rupiah($p->grand_total) }}</td>
                    <td>{{ format_rupiah($p->paid_amount) }}</td>
                    <td><span class="badge bg-{{ $p->payment_status == 'lunas' ? 'success' : 'warning' }}">{{ ucfirst($p->payment_status) }}</span></td>
                    <td><a href="{{ route('purchases.show', $p) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $purchases->links() }}
</div>
@endsection