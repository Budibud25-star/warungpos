@extends('layouts.app')
@section('title', 'Laporan Pengeluaran')
@section('content')
<div class="card p-3">
    <p>Total: <strong>{{ format_rupiah($total) }}</strong></p>
    <table class="table table-sm">
        <thead><tr><th>Tanggal</th><th>No</th><th>Kategori</th><th>Nominal</th><th>Deskripsi</th></tr></thead>
        <tbody>
            @foreach($expenses as $e)
            <tr><td>{{ $e->date->format('d/m/Y') }}</td><td>{{ $e->invoice_number }}</td><td>{{ $e->category->name }}</td><td>{{ format_rupiah($e->amount) }}</td><td>{{ $e->description }}</td></tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection