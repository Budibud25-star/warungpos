<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'Pelanggan Umum',  'phone' => '081234567890', 'is_member' => false, 'discount_percent' => 0],
            ['name' => 'Budi Santoso',    'phone' => '081234567891', 'is_member' => true,  'discount_percent' => 5],
            ['name' => 'Siti Aminah',     'phone' => '081234567892', 'is_member' => true,  'discount_percent' => 10],
            ['name' => 'Ahmad Fauzi',     'phone' => '081234567893', 'is_member' => true,  'discount_percent' => 5],
            ['name' => 'Dewi Lestari',    'phone' => '081234567894', 'is_member' => false, 'discount_percent' => 0],
            ['name' => 'Rudi Hartono',    'phone' => '081234567895', 'is_member' => false, 'discount_percent' => 0],
            ['name' => 'Nur Aini',        'phone' => '081234567896', 'is_member' => true,  'discount_percent' => 3],
            ['name' => 'Joko Widodo',     'phone' => '081234567897', 'is_member' => false, 'discount_percent' => 0],
            ['name' => 'Rina Marlina',    'phone' => '081234567898', 'is_member' => true,  'discount_percent' => 7.5],
            ['name' => 'Hendra Wijaya',   'phone' => '081234567899', 'is_member' => false, 'discount_percent' => 0],
        ];

        foreach ($data as $i => $d) {
            Customer::create(array_merge($d, [
                'code'         => 'CUST-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'member_since' => $d['is_member'] ? now() : null,
            ]));
        }
    }
}