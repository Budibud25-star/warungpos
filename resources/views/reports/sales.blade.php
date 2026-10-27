@extends('layouts.app')
@section('title', 'Laporan Penjualan')
@section('content')
<div class="card p-3">
    <form class="row g-2 mb-3">
        <div class="col-md-3">
            <select name="filter" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="today" @selected(request('filter')=='today')>Hari Ini</option>
                <option value="yesterday" @selected(request('filter')=='yesterday')>Kemarin</option>
                <option value="week" @selected(request('filter')=='week')>Minggu Ini</option>
                <option value="month" @selected(request('filter')=='month')>Bulan Ini</option>
                <option value="custom" @selected(request('filter')=='custom')>Custom</option>
            </select>
        </div>
        @if(request('filter')=='custom')
        <div class="col-md-3"><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}"></div>
        <div class="col-md-3"><input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}"></div>
        @endif
        <div class="col-md-3">
            <button class="btn btn-sm btn-primary">Filter</button>
            <a href="{{ route('reports.sales.pdf', request()->all()) }}" class="btn btn-sm btn-danger">PDF</a>
        </div>
    </form>
    <p class="small">Periode: {{ $from->format('d/m/Y') }} s/d {{ $to->format('d/m/Y') }} | Total: <strong>{{ format_rupiah($total) }}</strong></p>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead><tr><th>Tanggal</th><th>No</th><th>Kasir</th><th>Pelanggan</th><th>Subtotal</th><th>Diskon</th><th>Total</th><th>Metode</th></tr></thead>
            <tbody>
                @foreach($sales as $s)
                <tr>
                    <td>{{ $s->date->format('d/m/Y H:i') }}</td>
                    <td>{{ $s->invoice_number }}</td>
                    <td>{{ $s->user->name }}</td>
                    <td>{{ $s->customer->name ?? '-' }}</td>
                    <td>{{ format_rupiah($s->subtotal) }}</td>
                    <td>{{ format_rupiah($s->discount) }}</td>
                    <td>{{ format_rupiah($s->grand_total) }}</td>
                    <td>{{ ucfirst($s->payment_method) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection