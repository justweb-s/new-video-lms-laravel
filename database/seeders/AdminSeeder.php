<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'username' => 'admin',
            'email' => 'admin@videolms.com',
            'password' => Hash::make('password'),
            'first_name' => 'Admin',
            'last_name' => 'User',
            'is_active' => true,
        ]);

        Admin::create([
            'username' => 'manager',
            'email' => 'manager@videolms.com',
            'password' => Hash::make('password123'),
            'first_name' => 'Manager',
            'last_name' => 'System',
            'is_active' => true,
        ]);
    }
}
