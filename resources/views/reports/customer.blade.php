@extends('layouts.app')
@section('title', 'Laporan Pelanggan')
@section('content')
<div class="card p-3">
    <table class="table table-sm">
        <thead><tr><th>Kode</th><th>Nama</th><th>Telepon</th><th>Transaksi</th><th>Total Belanja</th></tr></thead>
        <tbody>
            @foreach($customers as $c)
            <tr>
                <td>{{ $c->code }}</td><td>{{ $c->name }}</td><td>{{ $c->phone }}</td>
                <td>{{ $c->sales_count }}</td>
                <td>{{ format_rupiah($c->sales_sum_grand_total ?? 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection