<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'invoice_number', 'customer_id', 'user_id', 'date',
        'subtotal', 'discount', 'discount_percent', 'tax', 'grand_total',
        'payment_method', 'paid_amount', 'change_amount',
        'status', 'notes', 'void_reason',
    ];

    protected $casts = [
        'date'             => 'datetime',
        'subtotal'         => 'decimal:2',
        'discount'         => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'tax'              => 'decimal:2',
        'grand_total'      => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}