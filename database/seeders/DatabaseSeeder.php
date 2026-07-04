<?php

namespace Database\Seeders;

<<<<<<< HEAD
=======
use App\Models\User;
use App\Models\Kategori;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
>>>>>>> enter/feature/loan-system
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Jalankan semua seeder secara berurutan
     * Urutan penting: Kategori harus ada sebelum Buku dibuat (karena FK)
     */
    public function run(): void
    {
<<<<<<< HEAD
        $this->call([
            KategoriSeeder::class, // 1. Kategori dulu
            UserSeeder::class,     // 2. User (admin + anggota dummy)
=======
        // Create admin user
        $admin = User::create([
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@moco.app',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create member users
        $member1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'role' => 'member',
        ]);

        $member2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
            'role' => 'member',
        ]);

        $member3 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => bcrypt('password'),
            'role' => 'member',
        ]);

        // Create categories
        $fiksi = Kategori::create(['nama' => 'Fiksi']);
        $nonFiksi = Kategori::create(['nama' => 'Non-Fiksi']);
        $pengembangan = Kategori::create(['nama' => 'Pengembangan Diri']);

        // Create books
        $books = [
            ['kategori_id' => $fiksi->id, 'judul' => 'Laskar Pelangi', 'penulis' => 'Andrea Hirata', 'penerbit' => 'Bentang', 'tahun_terbit' => 2005, 'isbn' => '978-0-123-45678-0', 'stok' => 5],
            ['kategori_id' => $fiksi->id, 'judul' => 'Bumi Manusia', 'penulis' => 'Pramoedya A.T.', 'penerbit' => 'Hasta Mitra', 'tahun_terbit' => 1980, 'isbn' => '978-0-123-45678-1', 'stok' => 3],
            ['kategori_id' => $nonFiksi->id, 'judul' => 'Filosofi Teras', 'penulis' => 'Henry Manampiring', 'penerbit' => 'Kompas', 'tahun_terbit' => 2018, 'isbn' => '978-0-123-45678-2', 'stok' => 4],
            ['kategori_id' => $pengembangan->id, 'judul' => 'Atomic Habits', 'penulis' => 'James Clear', 'penerbit' => 'Gramedia', 'tahun_terbit' => 2018, 'isbn' => '978-0-123-45678-3', 'stok' => 6],
            ['kategori_id' => $nonFiksi->id, 'judul' => 'Sapiens', 'penulis' => 'Yuval Noah Harari', 'penerbit' => 'Gramedia', 'tahun_terbit' => 2014, 'isbn' => '978-0-123-45678-4', 'stok' => 2],
            ['kategori_id' => $fiksi->id, 'judul' => 'The Alchemist', 'penulis' => 'Paulo Coelho', 'penerbit' => 'Gramedia', 'tahun_terbit' => 1988, 'isbn' => '978-0-123-45678-5', 'stok' => 4],
            ['kategori_id' => $pengembangan->id, 'judul' => 'Deep Work', 'penulis' => 'Cal Newport', 'penerbit' => 'Gramedia', 'tahun_terbit' => 2016, 'isbn' => '978-0-123-45678-6', 'stok' => 3],
            ['kategori_id' => $fiksi->id, 'judul' => 'Dune', 'penulis' => 'Frank Herbert', 'penerbit' => 'Gramedia', 'tahun_terbit' => 1965, 'isbn' => '978-0-123-45678-7', 'stok' => 2],
        ];

        foreach ($books as $book) {
            Buku::create($book);
        }

        // Create sample loans for testing
        $book1 = Buku::first();
        $book2 = Buku::skip(1)->first();

        // Loan 1: Pending (waiting approval)
        $loan1 = Peminjaman::create([
            'user_id' => $member1->id,
            'status' => 'pending',
        ]);
        PeminjamanDetail::create([
            'peminjaman_id' => $loan1->id,
            'buku_id' => $book1->id,
            'jumlah' => 1,
        ]);

        // Loan 2: Active (approved, ongoing)
        $loan2 = Peminjaman::create([
            'user_id' => $member2->id,
            'status' => 'dipinjam',
            'tanggal_pinjam' => Carbon::now()->subDays(3),
            'jatuh_tempo' => Carbon::now()->addDays(4),
            'approved_at' => Carbon::now()->subDays(3),
            'approved_by' => $admin->id,
        ]);
        PeminjamanDetail::create([
            'peminjaman_id' => $loan2->id,
            'buku_id' => $book2->id,
            'jumlah' => 1,
        ]);

        // Loan 3: Overdue (terlambat)
        $loan3 = Peminjaman::create([
            'user_id' => $member1->id,
            'status' => 'dipinjam',
            'tanggal_pinjam' => Carbon::now()->subDays(15),
            'jatuh_tempo' => Carbon::now()->subDays(8),
            'approved_at' => Carbon::now()->subDays(15),
            'approved_by' => $admin->id,
        ]);
        PeminjamanDetail::create([
            'peminjaman_id' => $loan3->id,
            'buku_id' => $book1->id,
            'jumlah' => 1,
        ]);

        // Loan 4: Completed (returned with fine)
        $loan4 = Peminjaman::create([
            'user_id' => $member3->id,
            'status' => 'selesai',
            'tanggal_pinjam' => Carbon::now()->subDays(20),
            'jatuh_tempo' => Carbon::now()->subDays(13),
            'tanggal_kembali' => Carbon::now()->subDays(10),
            'denda' => 3 * 500, // 3 days late × Rp 500
            'approved_at' => Carbon::now()->subDays(20),
            'approved_by' => $admin->id,
        ]);
        PeminjamanDetail::create([
            'peminjaman_id' => $loan4->id,
            'buku_id' => $book2->id,
            'jumlah' => 1,
        ]);

        // Loan 5: Completed (returned on time)
        $loan5 = Peminjaman::create([
            'user_id' => $member2->id,
            'status' => 'selesai',
            'tanggal_pinjam' => Carbon::now()->subDays(15),
            'jatuh_tempo' => Carbon::now()->subDays(8),
            'tanggal_kembali' => Carbon::now()->subDays(8),
            'denda' => 0,
            'approved_at' => Carbon::now()->subDays(15),
            'approved_by' => $admin->id,
        ]);
        PeminjamanDetail::create([
            'peminjaman_id' => $loan5->id,
            'buku_id' => $book1->id,
            'jumlah' => 1,
>>>>>>> enter/feature/loan-system
        ]);

        // 3. Buku dummy — dibuat setelah kategori ada agar FK tidak error
        \App\Models\Buku::factory(20)->create();
    }
}

