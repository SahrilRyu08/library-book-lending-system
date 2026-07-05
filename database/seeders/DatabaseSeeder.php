<?php

namespace Database\Seeders;

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
            DemoLibrarySeeder::class
        ]);
    }
}
