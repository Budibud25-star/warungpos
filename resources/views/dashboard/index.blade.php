@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card stat-card p-3" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
            <small>Penjualan Hari Ini</small>
            <h4 class="mb-0">{{ format_rupiah($totalSalesToday) }}</h4>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card p-3" style="background: linear-gradient(135deg, #10b981, #059669);">
            <small>Transaksi Hari Ini</small>
            <h4 class="mb-0">{{ $totalTrxToday }}</h4>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card p-3" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
            <small>Keuntungan Hari Ini</small>
            <h4 class="mb-0">{{ format_rupiah($profitToday) }}</h4>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card p-3" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
            <small>Saldo Kas</small>
            <h4 class="mb-0">{{ format_rupiah($cashBalance) }}</h4>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card p-3">
            <small class="text-muted">Total Produk</small>
            <h5 class="mb-0">{{ $totalProducts }}</h5>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3">
            <small class="text-muted">Stok Menipis</small>
            <h5 class="mb-0 text-danger">{{ $lowStock }}</h5>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3">
            <small class="text-muted">Pembelian Bulan Ini</small>
            <h5 class="mb-0">{{ format_rupiah($totalPurchaseMonth) }}</h5>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3">
            <small class="text-muted">Pengeluaran Bulan Ini</small>
            <h5 class="mb-0">{{ format_rupiah($totalExpenseMonth) }}</h5>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card p-3">
            <h6>Penjualan 7 Hari Terakhir</h6>
            <canvas id="chart7" height="90"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <h6>Penjualan per Kategori</h6>
            <canvas id="chartCat" height="180"></canvas>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card p-3">
            <h6>Transaksi Terbaru</h6>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead><tr><th>No</th><th>Tanggal</th><th>Kasir</th><th>Total</th><th>Metode</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($recentSales as $s)
                        <tr>
                            <td><a href="{{ route('sales.show', $s) }}">{{ $s->invoice_number }}</a></td>
                            <td>{{ $s->date->format('d/m H:i') }}</td>
                            <td>{{ $s->user->name }}</td>
                            <td>{{ format_rupiah($s->grand_total) }}</td>
                            <td>{{ ucfirst($s->payment_method) }}</td>
                            <td>
                                @if($s->status === 'completed')<span class="badge bg-success">Selesai</span>
                                @else<span class="badge bg-danger">Void</span>@endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <h6>Produk Terlaris</h6>
            <ol class="mb-0">
                @foreach($bestSellers as $b)
                <li>{{ $b->product->name ?? '-' }} ({{ $b->total_qty }})</li>
                @endforeach
            </ol>
        </div>
    </div>
</div>

@push('scripts')
<script>
const c7 = @json($chart7Days);
new Chart(document.getElementById('chart7'), {
    type: 'line',
    data: {
        labels: c7.map(x => x.date),
        datasets: [{ label: 'Penjualan', data: c7.map(x => x.total), borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,.1)', tension: .3, fill: true }]
    }
});
const cat = @json($salesByCategory);
new Chart(document.getElementById('chartCat'), {
    type: 'doughnut',
    data: {
        labels: cat.map(x => x.name),
        datasets: [{ data: cat.map(x => x.total), backgroundColor: ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899','#84cc16','#f97316','#6b7280'] }]
    }
});
</script>
@endpush
@endsection