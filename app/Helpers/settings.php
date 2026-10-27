<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        static $settings = null;

        if ($settings === null) {
            try {
                $settings = cache()->remember('app_settings', 3600, function () {
                    return Setting::pluck('value', 'key')->toArray();
                });
            } catch (\Exception $e) {
                return $default;
            }
        }

        return $settings[$key] ?? $default;
    }
}

if (!function_exists('format_rupiah')) {
    function format_rupiah($value)
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}

if (!function_exists('generate_number')) {
    function generate_number($prefix, $model)
    {
        $date = date('Ymd');
        $count = $model::whereDate('created_at', today())->count() + 1;
        return $prefix . '-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}