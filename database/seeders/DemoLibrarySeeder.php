<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoLibrarySeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            /*
            |--------------------------------------------------------------------------
            | USER
            |--------------------------------------------------------------------------
            */

            User::create([
                'nama' => 'Administrator',
                'email' => 'admin@library.test',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);

            $members = collect();

            for ($i = 1; $i <= 15; $i++) {

                $members->push(

                    User::create([

                        'nama' => "Anggota $i",

                        'email' => "anggota$i@test.com",

                        'password' => Hash::make('password'),

                        'role' => 'anggota',

                    ])

                );
            }

            /*
            |--------------------------------------------------------------------------
            | KATEGORI
            |--------------------------------------------------------------------------
            */

            $kategoriData = [

                [
                    'nama_kategori' => 'Novel',
                    'deskripsi' => 'Koleksi Novel'
                ],

                [
                    'nama_kategori' => 'Teknologi',
                    'deskripsi' => 'Buku Teknologi'
                ],

                [
                    'nama_kategori' => 'Bisnis',
                    'deskripsi' => 'Bisnis dan Manajemen'
                ],

                [
                    'nama_kategori' => 'Sejarah',
                    'deskripsi' => 'Sejarah Dunia'
                ],

                [
                    'nama_kategori' => 'Pengembangan',
                    'deskripsi' => 'Self Improvement'
                ],

            ];

            $kategori = collect();

            foreach ($kategoriData as $item) {

                $kategori->push(

                    Kategori::create($item)

                );
            }

            /*
            |--------------------------------------------------------------------------
            | DATA BUKU
            |--------------------------------------------------------------------------
            */

            $books = [

                [
                    'judul' => 'Atomic Habits',
                    'penulis' => 'James Clear',
                    'penerbit' => 'Gramedia',
                    'kategori' => 'Pengembangan',
                    'cover' => 'images/books/atomic-habits.jpg',
                ],

                [
                    'judul' => 'Deep Work',
                    'penulis' => 'Cal Newport',
                    'penerbit' => 'Grand Central',
                    'kategori' => 'Pengembangan',
                    'cover' => 'images/books/deep-work.jpg',
                ],

                [
                    'judul' => 'Clean Code',
                    'penulis' => 'Robert C Martin',
                    'penerbit' => 'Prentice Hall',
                    'kategori' => 'Teknologi',
                    'cover' => 'images/books/clean-code.jpg',
                ],

                [
                    'judul' => 'Effective Java',
                    'penulis' => 'Joshua Bloch',
                    'penerbit' => 'Addison Wesley',
                    'kategori' => 'Teknologi',
                    'cover' => 'images/books/effective-java.jpg',
                ],

                [
                    'judul' => 'Spring in Action',
                    'penulis' => 'Craig Walls',
                    'penerbit' => 'Manning',
                    'kategori' => 'Teknologi',
                    'cover' => 'images/books/spring-action.jpg',
                ],

                [
                    'judul' => 'Laravel Up and Running',
                    'penulis' => 'Matt Stauffer',
                    'penerbit' => 'OReilly',
                    'kategori' => 'Teknologi',
                    'cover' => 'images/books/laravel.jpg',
                ],

                [
                    'judul' => 'Laskar Pelangi',
                    'penulis' => 'Andrea Hirata',
                    'penerbit' => 'Bentang',
                    'kategori' => 'Novel',
                    'cover' => 'images/books/laskar-pelangi.jpg',
                ],

                [
                    'judul' => 'Bumi Manusia',
                    'penulis' => 'Pramoedya A Toer',
                    'penerbit' => 'Lentera',
                    'kategori' => 'Novel',
                    'cover' => 'images/books/bumi-manusia.jpg',
                ],

                [
                    'judul' => 'Negeri 5 Menara',
                    'penulis' => 'Ahmad Fuadi',
                    'penerbit' => 'Gramedia',
                    'kategori' => 'Novel',
                    'cover' => 'images/books/negeri-5-menara.jpg',
                ],

                [
                    'judul' => 'Sapiens',
                    'penulis' => 'Yuval Harari',
                    'penerbit' => 'Harvill',
                    'kategori' => 'Sejarah',
                    'cover' => 'images/books/sapiens.jpg',
                ],

                [
                    'judul' => 'Rich Dad Poor Dad',
                    'penulis' => 'Robert Kiyosaki',
                    'penerbit' => 'Plata',
                    'kategori' => 'Bisnis',
                    'cover' => 'images/books/rich-dad.jpg',
                ],

                [
                    'judul' => 'The Lean Startup',
                    'penulis' => 'Eric Ries',
                    'penerbit' => 'Crown',
                    'kategori' => 'Bisnis',
                    'cover' => 'images/books/lean-startup.jpg',
                ],

            ];

            $bookCollection = collect();

            foreach ($books as $book) {

                $kategoriId = $kategori
                    ->firstWhere('nama_kategori', $book['kategori'])
                    ->id;

                $bookCollection->push(

                    Buku::create([

                        'kategori_id' => $kategoriId,

                        'judul' => substr($book['judul'], 0, 75),

                        'penulis' => substr($book['penulis'], 0, 50),

                        'penerbit' => substr($book['penerbit'], 0, 30),

                        'tahun_terbit' => rand(2016, 2025),

                        'isbn' => fake()->unique()->numerify('978#########'),

                        'deskripsi' => fake()->paragraph(),

                        'stok' => rand(8, 15),

                        'cover' => $book['cover'],

                    ])

                );
            }

            /*
            |--------------------------------------------------------------------------
            | BAGIAN 2
            |--------------------------------------------------------------------------
            |
            | Selanjutnya:
            | - Seeder Peminjaman
            | - Seeder Detail Peminjaman
            | - Pengurangan stok
            |
            */
            /*
            |--------------------------------------------------------------------------
            | PEMINJAMAN
            |--------------------------------------------------------------------------
            */

            foreach (range(1, 40) as $i) {

                $status = collect([
                    'dipinjam',
                    'dipinjam',
                    'dipinjam',
                    'terlambat',
                    'selesai',
                    'selesai',
                ])->random();

                $tanggalPinjam = now()->subDays(rand(5, 30));

                $jatuhTempo = (clone $tanggalPinjam)->addDays(7);

                $tanggalKembali = null;

                $denda = 0;

                /*
                |--------------------------------------------------------------------------
                | STATUS SELESAI
                |--------------------------------------------------------------------------
                */

                if ($status == 'selesai') {

                    $tanggalKembali = (clone $jatuhTempo)
                        ->addDays(rand(-2, 2));

                    if ($tanggalKembali->gt($jatuhTempo)) {

                        $hari = $tanggalKembali
                            ->diffInDays($jatuhTempo);

                        $denda = $hari * 5000;

                    }

                }

                /*
                |--------------------------------------------------------------------------
                | STATUS TERLAMBAT
                |--------------------------------------------------------------------------
                */

                if ($status == 'terlambat') {

                    $jatuhTempo = now()->subDays(rand(1, 10));

                    $denda = now()
                            ->diffInDays($jatuhTempo) * 5000;

                }

                $loan = Peminjaman::create([

                    'user_id' => $members->random()->id,

                    'tanggal_pinjam' => $tanggalPinjam,

                    'jatuh_tempo' => $jatuhTempo,

                    'tanggal_kembali' => $tanggalKembali,

                    'status' => $status,

                    'denda' => $denda,

                ]);

                /*
                |--------------------------------------------------------------------------
                | DETAIL PEMINJAMAN
                |--------------------------------------------------------------------------
                */

                $jumlahBuku = rand(1, 3);

                $selectedBooks = $bookCollection->random(
                    min(
                        $jumlahBuku,
                        $bookCollection->count()
                    )
                );

                foreach ($selectedBooks as $book) {

                    PeminjamanDetail::create([

                        'peminjaman_id' => $loan->id,

                        'buku_id' => $book->id,

                        'jumlah' => 1,

                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | KURANGI STOK
                    |--------------------------------------------------------------------------
                    */

                    if (
                        in_array(
                            $status,
                            [
                                'dipinjam',
                                'terlambat'
                            ]
                        )
                    ) {

                        if ($book->stok > 0) {

                            $book->decrement(
                                'stok',
                                1
                            );

                            $book->refresh();

                        }

                    }

                }

            }

        });

    }

}
