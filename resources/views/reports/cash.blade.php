@extends('layouts.app')
@section('title', 'Laporan Kas')
@section('content')
<div class="card p-3">
    <p>Kas Masuk: <strong class="text-success">{{ format_rupiah($totalIn) }}</strong> | Kas Keluar: <strong class="text-danger">{{ format_rupiah($totalOut) }}</strong> | Saldo: <strong>{{ format_rupiah($balance) }}</strong></p>
    <table class="table table-sm">
        <thead><tr><th>Tanggal</th><th>Tipe</th><th>Nominal</th><th>Deskripsi</th></tr></thead>
        <tbody>
            @foreach($transactions as $t)
            <tr><td>{{ $t->date->format('d/m/Y') }}</td><td>{{ $t->type }}</td><td>{{ format_rupiah($t->amount) }}</td><td>{{ $t->description }}</td></tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection