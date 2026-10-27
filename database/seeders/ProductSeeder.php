<?php
namespace Database\Seeders;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [1, 1, 'Beras Premium 5kg', 'karung', 55000, 65000, 20],
            [1, 1, 'Minyak Goreng 2L', 'botol', 28000, 33000, 30],
            [1, 1, 'Gula Pasir 1kg', 'kg', 13000, 15000, 40],
            [1, 1, 'Tepung Terigu 1kg', 'kg', 10000, 12000, 35],
            [1, 1, 'Telur Ayam 1kg', 'kg', 25000, 29000, 25],
            [1, 1, 'Garam Halus 250g', 'bungkus', 2500, 4000, 50],
            [1, 1, 'Kecap Manis 275ml', 'botol', 8000, 10000, 30],
            [1, 1, 'Saus Sambal 335ml', 'botol', 9000, 12000, 25],
            [1, 1, 'Bawang Merah 1kg', 'kg', 30000, 35000, 15],
            [1, 1, 'Bawang Putih 1kg', 'kg', 28000, 33000, 15],
            [2, 3, 'Aqua 600ml', 'botol', 2500, 4000, 100],
            [2, 3, 'Aqua 1500ml', 'botol', 4500, 6500, 60],
            [2, 3, 'Teh Botol Sosro', 'botol', 3500, 5000, 80],
            [2, 3, 'Coca Cola 390ml', 'botol', 4000, 6000, 50],
            [2, 3, 'Fanta 390ml', 'botol', 4000, 6000, 50],
            [2, 3, 'Sprite 390ml', 'botol', 4000, 6000, 50],
            [2, 3, 'Kopi Kapal Api Sachet', 'sachet', 1200, 2000, 200],
            [2, 3, 'Susu Ultra 250ml', 'kotak', 5000, 7000, 60],
            [2, 3, 'Pocari Sweat 500ml', 'botol', 6000, 8000, 40],
            [2, 3, 'Mizone 500ml', 'botol', 4500, 6500, 45],
            [3, 2, 'Indomie Goreng', 'bungkus', 2500, 3500, 150],
            [3, 2, 'Indomie Kuah Ayam', 'bungkus', 2500, 3500, 150],
            [3, 2, 'Mie Sedaap Soto', 'bungkus', 2400, 3400, 120],
            [3, 2, 'Pop Mie Ayam', 'cup', 4500, 6000, 60],
            [3, 2, 'Sarimi Isi 2', 'bungkus', 3000, 4500, 80],
            [3, 2, 'Biskuit Roma Kelapa', 'pack', 7000, 9000, 40],
            [3, 2, 'Roti Tawar Sari Roti', 'pack', 12000, 15000, 20],
            [3, 2, 'Malkist Abon', 'pack', 8000, 10000, 35],
            [3, 2, 'Chitato Sapi 68g', 'bungkus', 8000, 10000, 50],
            [3, 2, 'Lays Rumput Laut', 'bungkus', 8000, 10000, 45],
            [4, 1, 'Gudang Garam Surya 12', 'bungkus', 22000, 25000, 50],
            [4, 1, 'Sampoerna Mild 16', 'bungkus', 27000, 30000, 40],
            [4, 1, 'Djarum Super 12', 'bungkus', 21000, 24000, 45],
            [4, 1, 'Marlboro Merah 20', 'bungkus', 35000, 40000, 20],
            [4, 1, 'Dji Sam Soe 12', 'bungkus', 23000, 26000, 30],
            [5, 1, 'Taro Net 70g', 'bungkus', 7000, 9000, 40],
            [5, 1, 'Qtela Singkong', 'bungkus', 7000, 9000, 40],
            [5, 1, 'Kusuka Kentang', 'bungkus', 7500, 9500, 35],
            [5, 1, 'Oreo Original', 'pack', 8000, 10000, 50],
            [5, 1, 'Tanggo Wafer', 'pack', 9000, 12000, 30],
            [6, 1, 'Sabun Cuci Piring 800ml', 'botol', 12000, 15000, 30],
            [6, 1, 'Detergen Rinso 800g', 'pack', 18000, 22000, 25],
            [6, 1, 'Pembersih Lantai 800ml', 'botol', 12000, 15000, 25],
            [6, 1, 'Sapu Ijuk', 'pcs', 15000, 20000, 15],
            [7, 1, 'Sabun Mandi Lifebuoy', 'pcs', 4000, 5500, 60],
            [7, 1, 'Shampo Sunsilk Sachet', 'sachet', 1000, 1500, 100],
            [7, 1, 'Pasta Gigi Pepsodent 190g', 'pcs', 12000, 15000, 30],
            [8, 1, 'Pampers M20', 'pack', 45000, 52000, 15],
            [9, 1, 'Tissue Paseo 250s', 'pack', 8000, 10000, 50],
            [10, 1, 'Baterai ABC AA', 'pcs', 3000, 4500, 40],
        ];

        foreach ($products as $i => $p) {
            Product::create([
                'category_id' => $p[0],
                'supplier_id' => $p[1],
                'code' => 'PRD-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'barcode' => '89912345' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'name' => $p[2],
                'unit' => $p[3],
                'purchase_price' => $p[4],
                'selling_price' => $p[5],
                'stock' => $p[6],
                'min_stock' => 5,
                'status' => 1,
            ]);
        }
    }
}