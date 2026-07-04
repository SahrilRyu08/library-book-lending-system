<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class NotificationBukuSeeder extends Seeder
{
    public function run(): void
    {
        $kategoriIds = Kategori::pluck('id', 'nama_kategori');

        $bukus = [
            [
                'judul' => 'Laskar Pelangi',
                'penulis' => 'Andrea Hirata',
                'penerbit' => 'Bentang Pustaka',
                'tahun_terbit' => 2005,
                'isbn' => '9789793062792',
                'deskripsi' => 'Novel tentang perjuangan anak-anak Belitung dalam mengejar pendidikan.',
                'kategori' => 'Fiksi',
                'stok' => 5,
            ],
            [
                'judul' => 'Bumi Manusia',
                'penulis' => 'Pramoedya Ananta Toer',
                'penerbit' => 'Lentera Dipantara',
                'tahun_terbit' => 1980,
                'isbn' => '9789799731234',
                'deskripsi' => 'Novel sejarah Indonesia pada masa kolonial.',
                'kategori' => 'Fiksi',
                'stok' => 3,
            ],
            [
                'judul' => 'Sapiens',
                'penulis' => 'Yuval Noah Harari',
                'penerbit' => 'Harvill Secker',
                'tahun_terbit' => 2014,
                'isbn' => '9780062316097',
                'deskripsi' => 'Sejarah singkat evolusi umat manusia.',
                'kategori' => 'Non-Fiksi',
                'stok' => 4,
            ],
            [
                'judul' => 'Clean Code',
                'penulis' => 'Robert C. Martin',
                'penerbit' => 'Prentice Hall',
                'tahun_terbit' => 2008,
                'isbn' => '9780132350884',
                'deskripsi' => 'Panduan menulis kode yang bersih dan mudah dipelihara.',
                'kategori' => 'Teknologi',
                'stok' => 6,
            ],
            [
                'judul' => 'Designing Data-Intensive Applications',
                'penulis' => 'Martin Kleppmann',
                'penerbit' => 'O\'Reilly Media',
                'tahun_terbit' => 2017,
                'isbn' => '9781449373320',
                'deskripsi' => 'Pembahasan sistem terdistribusi dan database modern.',
                'kategori' => 'Teknologi',
                'stok' => 2,
            ],
            [
                'judul' => 'Sejarah Indonesia Modern',
                'penulis' => 'M. C. Ricklefs',
                'penerbit' => 'Gadjah Mada University Press',
                'tahun_terbit' => 2008,
                'isbn' => '9789794207376',
                'deskripsi' => 'Kajian sejarah Indonesia dari masa kolonial hingga modern.',
                'kategori' => 'Sejarah',
                'stok' => 3,
            ],
            [
                'judul' => 'Cosmos',
                'penulis' => 'Carl Sagan',
                'penerbit' => 'Random House',
                'tahun_terbit' => 1980,
                'isbn' => '9780345539434',
                'deskripsi' => 'Eksplorasi alam semesta dan ilmu pengetahuan.',
                'kategori' => 'Sains',
                'stok' => 4,
            ],
            [
                'judul' => 'Filosofi Teras',
                'penulis' => 'Henry Manampiring',
                'penerbit' => 'Kompas',
                'tahun_terbit' => 2018,
                'isbn' => '9786024125189',
                'deskripsi' => 'Pengenalan filsafat Stoik untuk kehidupan sehari-hari.',
                'kategori' => 'Non-Fiksi',
                'stok' => 7,
            ],
        ];

        foreach ($bukus as $item) {
            Buku::updateOrCreate(
                ['judul' => $item['judul']],
                [
                    'penulis'      => $item['penulis'],
                    'penerbit'     => $item['penerbit'],
                    'tahun_terbit' => $item['tahun_terbit'],
                    'isbn'         => $item['isbn'],
                    'deskripsi'    => $item['deskripsi'],
                    'kategori_id'  => $kategoriIds[$item['kategori']] ?? null,
                    'stok'         => $item['stok'],
                ]
            );
        }
    }
}
