<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk {{ $sale->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; font-size: 12px; width: 80mm; margin: 0 auto; padding: 5px; }
        .text-center { text-align: center; } .text-right { text-align: right; }
        .header h1 { font-size: 14px; margin-bottom: 3px; }
        .header p { font-size: 11px; }
        .divider { border-top: 1px dashed #000; margin: 6px 0; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        td { padding: 1px 0; vertical-align: top; }
        .total-row td { font-weight: bold; font-size: 13px; }
        .no-print { margin-top: 20px; text-align: center; }
        .no-print a, .no-print button { margin: 2px; padding: 6px 12px; }
        @media print { .no-print { display: none; } body { width: 80mm; } }
    </style>
</head>
<body>
    <div class="header text-center">
        @if(setting('receipt_show_logo') == '1' && setting('store_logo'))
            <img src="{{ asset('storage/' . setting('store_logo')) }}" style="max-height: 40px;">
        @endif
        <h1>{{ setting('store_name') }}</h1>
        @if(setting('receipt_show_address') == '1') <p>{{ setting('store_address') }}</p> @endif
        @if(setting('receipt_show_phone') == '1') <p>Telp: {{ setting('store_phone') }}</p> @endif
    </div>

    <div class="divider"></div>

    <table>
        <tr><td>No</td><td>: {{ $sale->invoice_number }}</td></tr>
        <tr><td>Tanggal</td><td>: {{ $sale->date->format('d-m-Y H:i') }}</td></tr>
        @if(setting('receipt_show_cashier') == '1')
            <tr><td>Kasir</td><td>: {{ $sale->user->name }}</td></tr>
        @endif
        @if(setting('receipt_show_customer') == '1' && $sale->customer)
            <tr>
                <td>Pelanggan</td>
                <td>: {{ $sale->customer->name }}
                    @if($sale->customer->is_member) (Member)@endif
                </td>
            </tr>
        @endif
    </table>

    <div class="divider"></div>

    <table>
        @foreach($sale->items as $item)
        <tr>
            <td>{{ $item->product->name }}</td>
            <td class="text-center">{{ $item->quantity }}x</td>
            <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <table>
        <tr>
            <td>Subtotal</td>
            <td class="text-right">{{ number_format($sale->subtotal, 0, ',', '.') }}</td>
        </tr>

        @if($sale->discount_percent > 0)
        <tr>
            <td>Diskon Member ({{ $sale->discount_percent }}%)</td>
            <td class="text-right">
                -{{ number_format($sale->subtotal * $sale->discount_percent / 100, 0, ',', '.') }}
            </td>
        </tr>
        @endif

        @if($sale->discount > 0)
        <tr>
            <td>Total Diskon</td>
            <td class="text-right">-{{ number_format($sale->discount, 0, ',', '.') }}</td>
        </tr>
        @endif

        @if($sale->tax > 0)
        <tr>
            <td>Pajak</td>
            <td class="text-right">{{ number_format($sale->tax, 0, ',', '.') }}</td>
        </tr>
        @endif
    </table>

    <div class="divider"></div>

    <table>
        <tr class="total-row">
            <td>TOTAL</td>
            <td class="text-right">{{ number_format($sale->grand_total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Bayar</td>
            <td class="text-right">{{ number_format($sale->paid_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembali</td>
            <td class="text-right">{{ number_format($sale->change_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="text-center">
        <p>{{ setting('receipt_footer', 'Terima kasih telah berbelanja') }}</p>
    </div>

    <div class="no-print">
        <button onclick="window.print()">🖨️ Print</button>
        <a href="{{ route('pos.receipt.pdf', $sale->id) }}">📄 PDF</a>
        <a href="{{ route('pos.index') }}">➕ Transaksi Baru</a>
    </div>
</body>
</html>