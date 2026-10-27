<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'activity', 'module', 'data_id',
        'ip_address', 'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record($activity, $module = null, $dataId = null)
    {
        return self::create([
            'user_id' => auth()->id(),
            'activity' => $activity,
            'module' => $module,
            'data_id' => $dataId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}