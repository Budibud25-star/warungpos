<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\CashTransaction;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function createPurchase(array $data, $userId): Purchase
    {
        return DB::transaction(function () use ($data, $userId) {
            $invoiceNumber = 'PUR-' . date('Ymd') . '-' . str_pad(
                Purchase::whereDate('created_at', today())->count() + 1,
                4, '0', STR_PAD_LEFT
            );

            $subtotal = 0;
            $items = [];
            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $itemSubtotal = $item['price'] * $item['quantity'];
                $subtotal += $itemSubtotal;
                $items[] = compact('product', 'itemSubtotal') + [
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                ];
            }

            $discount = $data['discount'] ?? 0;
            $grandTotal = $subtotal - $discount;
            $paidAmount = $data['paid_amount'] ?? $grandTotal;

            $status = 'lunas';
            if ($paidAmount <= 0) $status = 'hutang';
            elseif ($paidAmount < $grandTotal) $status = 'sebagian';

            $purchase = Purchase::create([
                'invoice_number' => $invoiceNumber,
                'supplier_id' => $data['supplier_id'],
                'user_id' => $userId,
                'date' => $data['date'] ?? now(),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'payment_status' => $status,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['itemSubtotal'],
                ]);

                $stockBefore = $item['product']->stock;
                $stockAfter = $stockBefore + $item['quantity'];
                $item['product']->update([
                    'stock' => $stockAfter,
                    'purchase_price' => $item['price'],
                ]);

                StockMovement::create([
                    'product_id' => $item['product']->id,
                    'user_id' => $userId,
                    'type' => 'purchase',
                    'quantity' => $item['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reference' => $invoiceNumber,
                    'notes' => 'Pembelian',
                ]);
            }

            if ($paidAmount > 0) {
                CashTransaction::create([
                    'user_id' => $userId,
                    'type' => 'out',
                    'amount' => $paidAmount,
                    'description' => "Pembelian {$invoiceNumber}",
                    'reference_type' => 'purchase',
                    'reference_id' => $purchase->id,
                    'date' => today(),
                ]);
            }

            return $purchase;
        });
    }
}