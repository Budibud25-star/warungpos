<?php
namespace Database\Seeders;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Sembako', 'Minuman', 'Makanan', 'Rokok', 'Snack',
            'Peralatan Rumah', 'Kebersihan', 'Perawatan Diri', 'Bayi', 'Lainnya',
        ];
        foreach ($data as $name) Category::create(['name' => $name, 'status' => 1]);
    }
}