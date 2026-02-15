<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@mediflow.com',
            'password' => Hash::make('admin'),
            'role' => 'admin',
            'phone' => '+855 12 345 678',
        ]);
    }
}
