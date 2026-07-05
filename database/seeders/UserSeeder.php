<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed data user awal:
     * - 1 akun admin untuk mengelola sistem
     * - 10 akun anggota dummy untuk keperluan testing
     *
     * Semua password menggunakan 'password' (di-hash bcrypt)
     */
    public function run(): void
    {
        // Akun admin — login dengan admin@moco.app / password
        User::create([
            'nama'     => 'Admin MOCO',
            'email'    => 'admin@moco.app',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // 10 anggota dummy menggunakan UserFactory
        User::factory(10)->create();
    }
}
