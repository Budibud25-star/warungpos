@extends('layouts.app')
@section('title', 'Detail Pelanggan')
@section('content')
<div class="card p-3">
    <h5>{{ $customer->name }} <small class="text-muted">({{ $customer->code }})</small></h5>
    <p>Total Pembelian: <strong>{{ format_rupiah($total) }}</strong></p>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead><tr><th>No</th><th>Tanggal</th><th>Kasir</th><th>Total</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($sales as $s)
                <tr>
                    <td><a href="{{ route('sales.show', $s) }}">{{ $s->invoice_number }}</a></td>
                    <td>{{ $s->date->format('d/m/Y H:i') }}</td>
                    <td>{{ $s->user->name }}</td>
                    <td>{{ format_rupiah($s->grand_total) }}</td>
                    <td>{{ $s->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $sales->links() }}
</div>
@endsection