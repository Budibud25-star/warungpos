<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\StockMovement;
use App\Models\CashTransaction;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function createSale(array $data, $userId): Sale
    {
        return DB::transaction(function () use ($data, $userId) {
            // 1. Nomor invoice
            $invoiceNumber = 'TRX-' . date('Ymd') . '-' . str_pad(
                Sale::whereDate('created_at', today())->count() + 1,
                4, '0', STR_PAD_LEFT
            );

            // 2. Hitung subtotal & validasi stok
            $subtotal = 0;
            $items = [];
            foreach ($data['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi. Tersedia: {$product->stock}");
                }

                $itemDisc = $item['discount'] ?? 0;
                $itemSubtotal = ($product->selling_price * $item['quantity']) - $itemDisc;
                $subtotal += $itemSubtotal;

                $items[] = [
                    'product'      => $product,
                    'quantity'     => $item['quantity'],
                    'discount'     => $itemDisc,
                    'itemSubtotal' => $itemSubtotal,
                ];
            }

            // 3. Ambil customer & hitung diskon member
            $customer = null;
            if (!empty($data['customer_id'])) {
                $customer = Customer::find($data['customer_id']);
            }

            $memberDiscountPercent = 0;
            $memberDiscount = 0;
            if ($customer && $customer->isMember()) {
                $memberDiscountPercent = $customer->discount_percent;
                $memberDiscount = $subtotal * ($memberDiscountPercent / 100);
            }

            // 4. Diskon manual dari kasir
            $manualDiscount = $data['discount'] ?? 0;

            // 5. Total diskon & pajak
            $discount = $memberDiscount + $manualDiscount;
            $tax = 0;
            if (setting('tax_enabled') == '1') {
                $tax = ($subtotal - $discount) * (setting('tax_percentage', 10) / 100);
            }
            $grandTotal = $subtotal - $discount + $tax;

            // 6. Validasi pembayaran cash
            if ($data['payment_method'] === 'cash' && $data['paid_amount'] < $grandTotal) {
                throw new \Exception('Uang dibayar kurang dari total.');
            }
            $changeAmount = max(0, $data['paid_amount'] - $grandTotal);

            // 7. Simpan Sale
            $sale = Sale::create([
                'invoice_number'   => $invoiceNumber,
                'customer_id'      => $data['customer_id'] ?? null,
                'user_id'          => $userId,
                'date'             => now(),
                'subtotal'         => $subtotal,
                'discount'         => $discount,
                'discount_percent' => $memberDiscountPercent,
                'tax'              => $tax,
                'grand_total'      => $grandTotal,
                'payment_method'   => $data['payment_method'],
                'paid_amount'      => $data['paid_amount'],
                'change_amount'    => $changeAmount,
                'status'           => 'completed',
                'notes'            => $data['notes'] ?? null,
            ]);

            // 8. Simpan items + kurangi stok + stock movement
            foreach ($items as $item) {
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item['product']->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $item['product']->selling_price,
                    'discount'   => $item['discount'],
                    'subtotal'   => $item['itemSubtotal'],
                ]);

                $stockBefore = $item['product']->stock;
                $stockAfter  = $stockBefore - $item['quantity'];
                $item['product']->update(['stock' => $stockAfter]);

                StockMovement::create([
                    'product_id'   => $item['product']->id,
                    'user_id'      => $userId,
                    'type'         => 'sale',
                    'quantity'     => -$item['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after'  => $stockAfter,
                    'reference'    => $invoiceNumber,
                    'notes'        => 'Penjualan',
                ]);
            }

            // 9. Simpan payment
            Payment::create([
                'sale_id' => $sale->id,
                'method'  => $data['payment_method'],
                'amount'  => $data['paid_amount'],
            ]);

            // 10. Catat kas masuk
            CashTransaction::create([
                'user_id'        => $userId,
                'type'           => 'in',
                'amount'         => $grandTotal,
                'description'    => "Penjualan {$invoiceNumber}",
                'reference_type' => 'sale',
                'reference_id'   => $sale->id,
                'date'           => today(),
            ]);

            return $sale;
        });
    }

    public function voidSale(Sale $sale, string $reason, $userId): Sale
    {
        return DB::transaction(function () use ($sale, $reason, $userId) {
            if ($sale->status === 'void') {
                throw new \Exception('Transaksi sudah di-void.');
            }

            foreach ($sale->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                $stockBefore = $product->stock;
                $stockAfter  = $stockBefore + $item->quantity;
                $product->update(['stock' => $stockAfter]);

                StockMovement::create([
                    'product_id'   => $product->id,
                    'user_id'      => $userId,
                    'type'         => 'return',
                    'quantity'     => $item->quantity,
                    'stock_before' => $stockBefore,
                    'stock_after'  => $stockAfter,
                    'reference'    => $sale->invoice_number,
                    'notes'        => 'Void transaksi: ' . $reason,
                ]);
            }

            CashTransaction::create([
                'user_id'        => $userId,
                'type'           => 'out',
                'amount'         => $sale->grand_total,
                'description'    => "Void transaksi {$sale->invoice_number}",
                'reference_type' => 'sale_void',
                'reference_id'   => $sale->id,
                'date'           => today(),
            ]);

            $sale->update([
                'status'      => 'void',
                'void_reason' => $reason,
            ]);

            return $sale;
        });
    }
}