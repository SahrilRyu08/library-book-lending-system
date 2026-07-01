<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Seed data kategori buku awal
     * Dipakai sebagai referensi FK di tabel buku
     * Wajib dijalankan sebelum BukuFactory
     */
    public function run(): void
    {
        $data = [
            ['nama_kategori' => 'Fiksi',             'deskripsi' => 'Novel, cerpen, dan karya fiksi lainnya'],
            ['nama_kategori' => 'Non-Fiksi',         'deskripsi' => 'Buku berbasis fakta dan referensi'],
            ['nama_kategori' => 'Pengembangan Diri', 'deskripsi' => 'Self-help dan produktivitas'],
            ['nama_kategori' => 'Sains',             'deskripsi' => 'Ilmu pengetahuan alam dan teknologi'],
            ['nama_kategori' => 'Sejarah',           'deskripsi' => 'Sejarah dunia dan Indonesia'],
        ];

        foreach ($data as $item) {
            Kategori::create($item);
        }
    }
}
