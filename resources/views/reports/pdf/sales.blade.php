<!DOCTYPE html>
<html><head><meta charset="utf-8"><style>
body { font-family: sans-serif; font-size: 11px; }
table { width: 100%; border-collapse: collapse; }
th, td { border: 1px solid #ccc; padding: 4px; }
th { background: #eee; }
</style></head><body>
<h3>{{ setting('store_name') }}</h3>
<p>Laporan Penjualan {{ $from->format('d/m/Y') }} - {{ $to->format('d/m/Y') }}</p>
<table>
<tr><th>Tanggal</th><th>No</th><th>Kasir</th><th>Pelanggan</th><th>Total</th></tr>
@foreach($sales as $s)
<tr>
    <td>{{ $s->date->format('d/m/Y H:i') }}</td>
    <td>{{ $s->invoice_number }}</td>
    <td>{{ $s->user->name }}</td>
    <td>{{ $s->customer->name ?? '-' }}</td>
    <td>{{ number_format($s->grand_total, 0, ',', '.') }}</td>
</tr>
@endforeach
<tr><td colspan="4"><strong>Total</strong></td><td><strong>{{ number_format($total, 0, ',', '.') }}</strong></td></tr>
</table>
</body></html>