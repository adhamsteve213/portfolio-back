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
        User::query()->updateOrCreate([
            'email' => env('ADMIN_EMAIL', 'admin@portfolio.test'),
        ], [
            'name' => env('ADMIN_NAME', 'Portfolio Admin'),
            'password' => Hash::make(env('ADMIN_PASSWORD', 'admin12345')),
            'is_admin' => true,
        ]);
    }
}
