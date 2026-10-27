<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code', 'name', 'phone', 'whatsapp', 'email', 'address', 'notes',
        'is_member', 'discount_percent', 'member_since',
    ];

    protected $casts = [
        'is_member'        => 'boolean',
        'discount_percent' => 'decimal:2',
        'member_since'     => 'date',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function isMember()
    {
        return $this->is_member && $this->discount_percent > 0;
    }
}