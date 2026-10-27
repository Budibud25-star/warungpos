<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'role_id' => 1, 'name' => 'Admin Warung', 'email' => 'admin@warung.test',
            'username' => 'admin', 'password' => Hash::make('password'), 'status' => 'active',
        ]);
        User::create([
            'role_id' => 2, 'name' => 'Kasir Warung', 'email' => 'kasir@warung.test',
            'username' => 'kasir', 'password' => Hash::make('password'), 'status' => 'active',
        ]);
        User::create([
            'role_id' => 3, 'name' => 'Owner Warung', 'email' => 'owner@warung.test',
            'username' => 'owner', 'password' => Hash::make('password'), 'status' => 'active',
        ]);
    }
}