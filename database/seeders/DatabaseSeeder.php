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
            'email' => env('ADMIN_EMAIL', 'adhamsteve213@gmail.com'),
        ], [
            'name' => env('ADMIN_NAME', 'Portfolio Admin'),
            'password' => Hash::make(env('ADMIN_PASSWORD', 'mms183121')),
            'is_admin' => true,
        ]);
    }
}
