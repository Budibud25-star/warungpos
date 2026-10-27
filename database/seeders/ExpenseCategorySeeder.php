<?php
namespace Database\Seeders;
use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $data = ['Listrik', 'Internet', 'Transportasi', 'Belanja Operasional', 'Gaji', 'Peralatan', 'Lainnya'];
        foreach ($data as $name) ExpenseCategory::create(['name' => $name]);
    }
}