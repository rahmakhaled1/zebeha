<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'f-name' => 'Admin',
            'l-name' => '1',
            'email' => 'admin@example.com',
            'phone' => '1234567890',
            "password" => Hash::make("password"),
            'is_admin' => true,
            'is_verified' => true,
        ]);
    }
}
