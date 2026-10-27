<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
use Config;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Pagination pakai Bootstrap 5
        Paginator::useBootstrapFive();

        // Set timezone global (PHP + Carbon + MySQL)
        $timezone = config('app.timezone', 'Asia/Jakarta');
        date_default_timezone_set($timezone);

        // Set timezone Carbon (untuk perhitungan now(), today(), dll)
        Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID', 'id_ID.utf8', 'Indonesian_indonesia');

        // Set timezone MySQL agar NOW() juga ikut Asia/Jakarta
        Config::set('database.connections.mysql.timezone', '+07:00');
    }
}