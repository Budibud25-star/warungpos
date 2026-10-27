@extends('layouts.app')
@section('title', 'Riwayat Transaksi')
@section('content')
<div class="card p-3">
    <form class="row g-2 mb-3">
        <div class="col-md-3"><input type="text" name="search" class="form-control form-control-sm" placeholder="No. transaksi" value="{{ request('search') }}"></div>
        <div class="col-md-2"><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}"></div>
        <div class="col-md-2"><input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}"></div>
        <div class="col-md-2">
            <select name="user_id" class="form-select form-select-sm">
                <option value="">Semua Kasir</option>
                @foreach($users as $u)<option value="{{ $u->id }}" @selected(request('user_id')==$u->id)>{{ $u->name }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="payment_method" class="form-select form-select-sm">
                <option value="">Semua Metode</option>
                @foreach(['cash','transfer','qris','debit','kredit','ewallet'] as $m)
                <option value="{{ $m }}" @selected(request('payment_method')==$m)>{{ ucfirst($m) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1"><button class="btn btn-sm btn-primary w-100">Filter</button></div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light"><tr><th>No</th><th>Tanggal</th><th>Kasir</th><th>Pelanggan</th><th>Total</th><th>Metode</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($sales as $s)
                <tr>
                    <td>{{ $s->invoice_number }}</td>
                    <td>{{ $s->date->format('d/m/Y H:i') }}</td>
                    <td>{{ $s->user->name }}</td>
                    <td>{{ $s->customer->name ?? '-' }}</td>
                    <td>{{ format_rupiah($s->grand_total) }}</td>
                    <td>{{ ucfirst($s->payment_method) }}</td>
                    <td>{!! $s->status === 'completed' ? '<span class="badge bg-success">Selesai</span>' : '<span class="badge bg-danger">Void</span>' !!}</td>
                    <td>
                        <a href="{{ route('sales.show', $s) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('pos.receipt', $s) }}" target="_blank" class="btn btn-sm btn-secondary"><i class="bi bi-printer"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $sales->withQueryString()->links() }}
</div>
@endsection