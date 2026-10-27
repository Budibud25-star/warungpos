<?php
namespace Database\Seeders;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'description' => 'Administrator dengan akses penuh'],
            ['name' => 'kasir', 'description' => 'Kasir untuk transaksi penjualan'],
            ['name' => 'owner', 'description' => 'Pemilik toko dengan akses laporan'],
        ];
        foreach ($roles as $r) Role::create($r);
    }
}