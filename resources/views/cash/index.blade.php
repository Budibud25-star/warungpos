@extends('layouts.app')
@section('title', 'Kas')
@section('content')

<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card p-3"><small>Total Kas Masuk</small><h4 class="text-success">{{ format_rupiah($totalIn) }}</h4></div></div>
    <div class="col-md-4"><div class="card p-3"><small>Total Kas Keluar</small><h4 class="text-danger">{{ format_rupiah($totalOut) }}</h4></div></div>
    <div class="col-md-4"><div class="card p-3"><small>Saldo Akhir</small><h4>{{ format_rupiah($balance) }}</h4></div></div>
</div>

<div class="card p-3 mb-3">
    <h6>Transaksi Kas Manual</h6>
    <form action="{{ route('cash.store') }}" method="POST" class="row g-2">
        @csrf
        <div class="col-md-2">
            <select name="type" class="form-select form-select-sm" required>
                <option value="in">Kas Masuk</option>
                <option value="out">Kas Keluar</option>
            </select>
        </div>
        <div class="col-md-2"><input type="date" name="date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required></div>
        <div class="col-md-2"><input type="number" name="amount" class="form-control form-control-sm" placeholder="Nominal" min="0" required></div>
        <div class="col-md-5"><input type="text" name="description" class="form-control form-control-sm" placeholder="Deskripsi" required></div>
        <div class="col-md-1"><button class="btn btn-sm btn-primary w-100">Simpan</button></div>
    </form>
</div>

<div class="card p-3">
    <h6>Riwayat Kas</h6>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light"><tr><th>Tanggal</th><th>Tipe</th><th>Nominal</th><th>Deskripsi</th><th>User</th></tr></thead>
            <tbody>
                @foreach($transactions as $t)
                <tr>
                    <td>{{ $t->date->format('d/m/Y') }}</td>
                    <td>{!! $t->type == 'in' ? '<span class="badge bg-success">Masuk</span>' : '<span class="badge bg-danger">Keluar</span>' !!}</td>
                    <td>{{ format_rupiah($t->amount) }}</td>
                    <td>{{ $t->description }}</td>
                    <td>{{ $t->user->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $transactions->links() }}
</div>
@endsection