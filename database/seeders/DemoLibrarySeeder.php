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

class   DemoLibrarySeeder extends Seeder
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
                'nama' => 'Admin MOCO',
                'email' => 'admin@moco.app',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);

            $members = collect();

            $newUsersData = [
                ['nama' => 'Sahril', 'email' => 'sahril@moco.app'],
                ['nama' => 'Zhavira', 'email' => 'zhavira@moco.app'],
                ['nama' => 'David', 'email' => 'david@moco.app'],
                ['nama' => 'Adid', 'email' => 'adid@moco.app'],
            ];

            foreach ($newUsersData as $userData) {
                $newUser = User::create([
                    'nama' => $userData['nama'],
                    'email' => $userData['email'],
                    'password' => Hash::make('password'),
                    'role' => 'anggota',
                ]);
                $members->push($newUser);
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

            // Create exactly 2 terlambat loans
            for ($i = 0; $i < 2; $i++) {
                $status = 'terlambat';
                $jatuhTempo = now()->subDays(rand(1, 10));
                $tanggalPinjam = (clone $jatuhTempo)->subDays(7);
                $tanggalKembali = null;
                $denda = abs(now()->diffInDays($jatuhTempo)) * config('library.denda_per_hari', 1000);

                $loan = Peminjaman::create([
                    'user_id' => $members[$i]->id,
                    'tanggal_pinjam' => $tanggalPinjam,
                    'jatuh_tempo' => $jatuhTempo,
                    'tanggal_kembali' => $tanggalKembali,
                    'status' => $status,
                    'denda' => $denda,
                ]);

                $selectedBook = $bookCollection[$i];
                PeminjamanDetail::create([
                    'peminjaman_id' => $loan->id,
                    'buku_id' => $selectedBook->id,
                    'jumlah' => 1,
                ]);
                if ($selectedBook->stok > 0) {
                    $selectedBook->decrement('stok', 1);
                    $selectedBook->refresh();
                }
            }

            // Create exactly 1 sedang dipinjam loan (for testing return)
            $status = 'dipinjam';
            $tanggalPinjam = now()->subDays(1);
            $jatuhTempo = (clone $tanggalPinjam)->addDays(7);
            $tanggalKembali = null;
            $denda = 0;

            $loan = Peminjaman::create([
                'user_id' => $members[0]->id, // Sahril
                'tanggal_pinjam' => $tanggalPinjam,
                'jatuh_tempo' => $jatuhTempo,
                'tanggal_kembali' => $tanggalKembali,
                'status' => $status,
                'denda' => $denda,
            ]);

            $selectedBook = $bookCollection[2]; // Clean Code
            PeminjamanDetail::create([
                'peminjaman_id' => $loan->id,
                'buku_id' => $selectedBook->id,
                'jumlah' => 1,
            ]);
            if ($selectedBook->stok > 0) {
                $selectedBook->decrement('stok');
                $selectedBook->refresh();
            }

            // Create exactly 1 terlambat loan (for testing return with denda)
            $status = 'terlambat';
            $tanggalPinjam = now()->subDays(14);
            $jatuhTempo = (clone $tanggalPinjam)->addDays(7); // 7 days ago
            $tanggalKembali = null;
            $denda = 0; // Not calculated yet, will be calculated on return

            $loan = Peminjaman::create([
                'user_id' => $members[1]->id, // Zhavira
                'tanggal_pinjam' => $tanggalPinjam,
                'jatuh_tempo' => $jatuhTempo,
                'tanggal_kembali' => $tanggalKembali,
                'status' => $status,
                'denda' => $denda,
            ]);

            $selectedBook = $bookCollection[3]; // Effective Java
            PeminjamanDetail::create([
                'peminjaman_id' => $loan->id,
                'buku_id' => $selectedBook->id,
                'jumlah' => 1,
            ]);
            if ($selectedBook->stok > 0) {
                $selectedBook->decrement('stok');
                $selectedBook->refresh();
            }

            // Create 1 selesai tepat waktu
            $selesaiTepatLoan = Peminjaman::create([
                'user_id' => $members[0]->id,
                'tanggal_pinjam' => now()->subDays(14),
                'jatuh_tempo' => now()->subDays(7),
                'tanggal_kembali' => now()->subDays(8),
                'status' => 'selesai',
                'denda' => 0,
            ]);
            PeminjamanDetail::create([
                'peminjaman_id' => $selesaiTepatLoan->id,
                'buku_id' => $bookCollection[4]->id,
                'jumlah' => 1,
            ]);

            // Create 1 selesai terlambat (with denda)
            $selesaiTerlambatLoan = Peminjaman::create([
                'user_id' => $members[1]->id,
                'tanggal_pinjam' => now()->subDays(20),
                'jatuh_tempo' => now()->subDays(13),
                'tanggal_kembali' => now()->subDays(10),
                'status' => 'selesai',
                'denda' => 3 * 5000,
            ]);
            PeminjamanDetail::create([
                'peminjaman_id' => $selesaiTerlambatLoan->id,
                'buku_id' => $bookCollection[5]->id,
                'jumlah' => 1,
            ]);

            // Create 1 more selesai tepat waktu for David
            $selesaiDavidLoan = Peminjaman::create([
                'user_id' => $members[2]->id,
                'tanggal_pinjam' => now()->subDays(30),
                'jatuh_tempo' => now()->subDays(23),
                'tanggal_kembali' => now()->subDays(25),
                'status' => 'selesai',
                'denda' => 0,
            ]);
            PeminjamanDetail::create([
                'peminjaman_id' => $selesaiDavidLoan->id,
                'buku_id' => $bookCollection[6]->id,
                'jumlah' => 1,
            ]);

        });

    }

}
