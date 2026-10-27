<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: 'Courier New', monospace; font-size: 10px; }
    .text-center { text-align: center; } .text-right { text-align: right; }
    table { width: 100%; border-collapse: collapse; }
    td { padding: 1px 0; }
    .divider { border-top: 1px dashed #000; margin: 4px 0; }
</style>
</head>
<body>
    <div class="text-center">
        <strong>{{ setting('store_name') }}</strong><br>
        {{ setting('store_address') }}<br>
        Telp: {{ setting('store_phone') }}
    </div>
    <div class="divider"></div>
    <table>
        <tr><td>No</td><td class="text-right">{{ $sale->invoice_number }}</td></tr>
        <tr><td>Tanggal</td><td class="text-right">{{ $sale->date->format('d-m-Y H:i') }}</td></tr>
        <tr><td>Kasir</td><td class="text-right">{{ $sale->user->name }}</td></tr>
    </table>
    <div class="divider"></div>
    <table>
        @foreach($sale->items as $item)
        <tr>
            <td>{{ $item->product->name }}</td>
            <td class="text-right">{{ $item->quantity }}x</td>
            <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>
    <div class="divider"></div>
    <table>
        <tr><td>Subtotal</td><td class="text-right">{{ number_format($sale->subtotal, 0, ',', '.') }}</td></tr>
        <tr><td>Diskon</td><td class="text-right">{{ number_format($sale->discount, 0, ',', '.') }}</td></tr>
        <tr><td>Pajak</td><td class="text-right">{{ number_format($sale->tax, 0, ',', '.') }}</td></tr>
        <tr><td><strong>TOTAL</strong></td><td class="text-right"><strong>{{ number_format($sale->grand_total, 0, ',', '.') }}</strong></td></tr>
        <tr><td>Bayar</td><td class="text-right">{{ number_format($sale->paid_amount, 0, ',', '.') }}</td></tr>
        <tr><td>Kembali</td><td class="text-right">{{ number_format($sale->change_amount, 0, ',', '.') }}</td></tr>
    </table>
    <div class="divider"></div>
    <div class="text-center">{{ setting('receipt_footer', 'Terima kasih telah berbelanja') }}</div>
</body>
</html>