@extends('layouts.app')
@section('title', 'Laporan Pembelian')
@section('content')

<div class="card p-3">
    <form class="row g-2 mb-3">
        <div class="col-md-3">
            <select name="filter" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="today" @selected(request('filter','today')=='today')>Hari Ini</option>
                <option value="yesterday" @selected(request('filter')=='yesterday')>Kemarin</option>
                <option value="week" @selected(request('filter')=='week')>Minggu Ini</option>
                <option value="month" @selected(request('filter')=='month')>Bulan Ini</option>
                <option value="custom" @selected(request('filter')=='custom')>Custom</option>
            </select>
        </div>
        @if(request('filter')=='custom')
        <div class="col-md-3">
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-3">
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
        </div>
        @endif
        <div class="col-md-3">
            <button class="btn btn-sm btn-primary">Filter</button>
        </div>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <p class="small mb-0">
            Periode: <strong>{{ $from->format('d/m/Y') }}</strong> s/d <strong>{{ $to->format('d/m/Y') }}</strong>
            | Total: <strong>{{ format_rupiah($total) }}</strong>
        </p>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>No. Pembelian</th>
                    <th>Supplier</th>
                    <th>User</th>
                    <th>Subtotal</th>
                    <th>Diskon</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $p)
                <tr>
                    <td>{{ $p->date->format('d/m/Y') }}</td>
                    <td>{{ $p->invoice_number }}</td>
                    <td>{{ $p->supplier->name ?? '-' }}</td>
                    <td>{{ $p->user->name ?? '-' }}</td>
                    <td>{{ format_rupiah($p->subtotal) }}</td>
                    <td>{{ format_rupiah($p->discount) }}</td>
                    <td><strong>{{ format_rupiah($p->grand_total) }}</strong></td>
                    <td>
                        @php
                            $badge = ['lunas' => 'success', 'hutang' => 'danger', 'sebagian' => 'warning'][$p->payment_status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $badge }}">{{ ucfirst($p->payment_status) }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-3">Tidak ada data pembelian pada periode ini</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection