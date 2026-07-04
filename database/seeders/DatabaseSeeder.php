<?php

namespace Database\Seeders;

use App\Models\Buku;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Jalankan semua seeder secara berurutan
     * Urutan penting: Kategori harus ada sebelum Buku dibuat (karena FK)
     */
    public function run(): void
    {
        $this->call([
            KategoriSeeder::class, // 1. Kategori dulu
            UserSeeder::class,     // 2. User (admin + anggota dummy)
            NotificationUserSeeder::class,
            NotificationKategoriseeder::class,
            NotificationBukuSeeder::class,
            NotificationPeminjamanSeeder::class,
            NotificationSeeder::class
        ]);

        // 3. Buku dummy — dibuat setelah kategori ada agar FK tidak error
        Buku::factory(20)->create();
    }
}
