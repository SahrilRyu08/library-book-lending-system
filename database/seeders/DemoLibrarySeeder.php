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

            $lamaPinjam = (int) config('library.max_hari_pinjam', 7);
            $dendaPerHari = (int) config('library.tarif_denda_per_hari', 1000);
            $hMinus = (int) config('library.notif_h_minus', 3);

            $buatLoan = function (
                User $member,
                Buku $book,
                string $status,
                $tanggalPinjam,
                $jatuhTempo,
                $tanggalKembali = null,
                int $denda = 0,
                bool $kurangiStok = false
            ) {
                $loan = Peminjaman::create([
                    'user_id' => $member->id,
                    'tanggal_pinjam' => $tanggalPinjam,
                    'jatuh_tempo' => $jatuhTempo,
                    'tanggal_kembali' => $tanggalKembali,
                    'status' => $status,
                    'denda' => $denda,
                ]);

                PeminjamanDetail::create([
                    'peminjaman_id' => $loan->id,
                    'buku_id' => $book->id,
                    'jumlah' => 1,
                ]);

                if ($kurangiStok && $book->stok > 0) {
                    $book->decrement('stok', 1);
                    $book->refresh();
                }

                return $loan;
            };

            // 2 peminjaman terlambat aktif untuk dashboard dan laporan
            $buatLoan(
                $members[0],
                $bookCollection[0],
                'terlambat',
                now()->subDays($lamaPinjam + 3),
                now()->subDays(3),
                null,
                3 * $dendaPerHari,
                true
            );

            $buatLoan(
                $members[1],
                $bookCollection[1],
                'terlambat',
                now()->subDays($lamaPinjam + 5),
                now()->subDays(5),
                null,
                5 * $dendaPerHari,
                true
            );

            // 2 transaksi aktif non-selesai:
            // 1 dipinjam dengan jatuh tempo H-3 untuk uji reminder
            $buatLoan(
                $members[2],
                $bookCollection[2],
                'dipinjam',
                now()->subDays($lamaPinjam - $hMinus),
                now()->addDays($hMinus),
                null,
                0,
                true
            );

            // 1 menunggu konfirmasi admin
            $buatLoan(
                $members[3],
                $bookCollection[3],
                'menunggu',
                now(),
                now()->addDays($lamaPinjam),
                null,
                0,
                true
            );

            // Riwayat selesai tepat waktu
            $buatLoan(
                $members[0],
                $bookCollection[4],
                'selesai',
                now()->subDays(14),
                now()->subDays(7),
                now()->subDays(8),
                0
            );

            // Riwayat selesai terlambat untuk uji total denda laporan
            $buatLoan(
                $members[1],
                $bookCollection[5],
                'selesai',
                now()->subDays(20),
                now()->subDays(13),
                now()->subDays(10),
                3 * $dendaPerHari
            );

            // Tambahan riwayat selesai tepat waktu agar buku populer lebih variatif
            $buatLoan(
                $members[2],
                $bookCollection[6],
                'selesai',
                now()->subDays(30),
                now()->subDays(23),
                now()->subDays(25),
                0
            );

        });

    }

}
