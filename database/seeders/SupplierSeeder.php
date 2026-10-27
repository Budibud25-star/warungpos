<?php
namespace Database\Seeders;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'PT Sumber Makmur', 'phone' => '021-1234567', 'address' => 'Jakarta'],
            ['name' => 'CV Pangan Sejahtera', 'phone' => '022-7654321', 'address' => 'Bandung'],
            ['name' => 'UD Minuman Segar', 'phone' => '031-1112223', 'address' => 'Surabaya'],
            ['name' => 'Toko Grosir Berkah', 'phone' => '0274-555666', 'address' => 'Yogyakarta'],
            ['name' => 'PT Sembako Nusantara', 'phone' => '024-777888', 'address' => 'Semarang'],
        ];
        foreach ($data as $d) Supplier::create(array_merge($d, ['status' => 1]));
    }
}