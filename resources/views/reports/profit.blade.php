@extends('layouts.app')
@section('title', 'Laporan Keuntungan')
@section('content')
<div class="card p-3">
    <form class="row g-2 mb-3">
        <div class="col-md-3">
            <select name="filter" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="today" @selected(request('filter','today')=='today')>Hari Ini</option>
                <option value="week" @selected(request('filter')=='week')>Minggu Ini</option>
                <option value="month" @selected(request('filter')=='month')>Bulan Ini</option>
            </select>
        </div>
    </form>
    <p>Periode: {{ $from->format('d/m/Y') }} - {{ $to->format('d/m/Y') }}</p>
    <p>Total Penjualan: <strong>{{ format_rupiah($totalSales) }}</strong></p>
    <p>Total Modal: <strong>{{ format_rupiah($totalModal) }}</strong></p>
    <h4>Keuntungan: {{ format_rupiah($profit) }}</h4>
</div>
@endsection