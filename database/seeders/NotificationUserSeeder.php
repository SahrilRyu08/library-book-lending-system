<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class NotificationUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@moco.test'],
            [
                'nama'     => 'Admin MOCO',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        $anggota = [
            ['nama' => 'Sahril Ryu',      'email' => 'sahril@moco.test'],
            ['nama' => 'Dewi Lestari',    'email' => 'dewi@moco.test'],
            ['nama' => 'Budi Santoso',    'email' => 'budi@moco.test'],
            ['nama' => 'Rina Amelia',     'email' => 'rina@moco.test'],
            ['nama' => 'Agus Setiawan',   'email' => 'agus@moco.test'],
        ];

        foreach ($anggota as $item) {
            User::updateOrCreate(
                ['email' => $item['email']],
                [
                    'nama'     => $item['nama'],
                    'password' => Hash::make('password'),
                    'role'     => 'anggota',
                ]
            );
        }
    }
}
