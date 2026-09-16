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
     * Membuat admin default SIM-ONT jika belum ada.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@simONT.local'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('admin123*#'),
            ]
        );
    }
}
