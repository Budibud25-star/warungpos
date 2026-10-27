<?php
namespace Database\Seeders;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'store_name', 'value' => 'Warung Maju Jaya', 'group' => 'store'],
            ['key' => 'store_address', 'value' => 'Jl. Raya Klaten No. 123, Klaten', 'group' => 'store'],
            ['key' => 'store_phone', 'value' => '081234567890', 'group' => 'store'],
            ['key' => 'store_email', 'value' => 'warungmajujaya@email.com', 'group' => 'store'],
            ['key' => 'store_owner', 'value' => 'H. Ahmad', 'group' => 'store'],
            ['key' => 'store_logo', 'value' => null, 'group' => 'store'],
            ['key' => 'receipt_footer', 'value' => 'Terima kasih telah berbelanja', 'group' => 'receipt'],
            ['key' => 'receipt_show_logo', 'value' => '1', 'type' => 'boolean', 'group' => 'receipt'],
            ['key' => 'receipt_show_address', 'value' => '1', 'type' => 'boolean', 'group' => 'receipt'],
            ['key' => 'receipt_show_phone', 'value' => '1', 'type' => 'boolean', 'group' => 'receipt'],
            ['key' => 'receipt_show_cashier', 'value' => '1', 'type' => 'boolean', 'group' => 'receipt'],
            ['key' => 'receipt_show_customer', 'value' => '1', 'type' => 'boolean', 'group' => 'receipt'],
            ['key' => 'receipt_show_discount', 'value' => '1', 'type' => 'boolean', 'group' => 'receipt'],
            ['key' => 'receipt_show_tax', 'value' => '1', 'type' => 'boolean', 'group' => 'receipt'],
            ['key' => 'receipt_width', 'value' => '80', 'type' => 'integer', 'group' => 'receipt'],
            ['key' => 'tax_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'payment'],
            ['key' => 'tax_percentage', 'value' => '10', 'type' => 'integer', 'group' => 'payment'],
        ];
        foreach ($settings as $s) Setting::create($s);
    }
}