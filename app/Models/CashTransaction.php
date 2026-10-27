<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    protected $fillable = [
        'user_id', 'type', 'amount', 'description',
        'reference_type', 'reference_id', 'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function balance()
    {
        $in = self::where('type', 'in')->sum('amount');
        $out = self::where('type', 'out')->sum('amount');
        return $in - $out;
    }
}