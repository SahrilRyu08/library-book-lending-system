<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Member BookController - DUMMY untuk preview UI
 * Semua data pakai objek stdClass palsu, tidak sentuh database.
 */
class BookController extends Controller
{
    // Helper: buat objek buku dummy
    private function dummyBooks(): array
    {
        $raw = [
            [1, 'Laskar Pelangi',     'Andrea Hirata',      'Fiksi',             3, 'Novel tentang anak-anak di Belitung yang penuh semangat mengejar pendidikan.'],
            [2, 'Bumi Manusia',       'Pramoedya A.T.',     'Fiksi',             1, 'Kisah Minke di era kolonial Belanda yang penuh gejolak.'],
            [3, 'Filosofi Teras',     'Henry Manampiring',  'Non-Fiksi',         0, 'Pengantar filsafat Stoa untuk kehidupan modern.'],
            [4, 'Atomic Habits',      'James Clear',        'Pengembangan Diri', 5, 'Cara membangun kebiasaan kecil yang berdampak besar.'],
            [5, 'Sapiens',            'Yuval Noah Harari',  'Non-Fiksi',         2, 'Sejarah singkat umat manusia dari zaman purba hingga modern.'],
            [6, 'The Alchemist',      'Paulo Coelho',       'Fiksi',             4, 'Perjalanan seorang gembala muda mengejar takdirnya.'],
            [7, 'Deep Work',          'Cal Newport',        'Pengembangan Diri', 3, 'Aturan sukses di dunia yang penuh distraksi.'],
            [8, 'Dune',               'Frank Herbert',      'Fiksi',             0, 'Epik fiksi ilmiah di planet gurun Arrakis.'],
        ];

        return array_map(function ($d) {
            $b = new \stdClass();
            $b->id          = $d[0];
            $b->judul       = $d[1];
            $b->penulis     = $d[2];
            $b->cover       = null;
            $b->stok        = $d[4];
            $b->deskripsi   = $d[5];
            $b->isbn        = '978-000-000-' . str_pad($d[0], 4, '0', STR_PAD_LEFT);
            $b->penerbit    = 'Gramedia';
            $b->tahun_terbit = 2020 + $d[0];
            $kat = new \stdClass();
            $kat->nama_kategori = $d[3];
            $b->kategori = $kat;
            return $b;
        }, $raw);
    }

    private function dummyCategories(): array
    {
        return array_map(function ($name) {
            $c = new \stdClass();
            $c->id = rand(1, 99);
            $c->nama_kategori = $name;
            return $c;
        }, ['Fiksi', 'Non-Fiksi', 'Pengembangan Diri']);
    }

    // ── Katalog ──────────────────────────────────────────
    public function index(Request $request)
    {
        $books = collect($this->dummyBooks());

        // Filter sederhana untuk preview
        if ($request->search) {
            $q = strtolower($request->search);
            $books = $books->filter(fn($b) =>
                str_contains(strtolower($b->judul), $q) ||
                str_contains(strtolower($b->penulis), $q)
            );
        }

        // Simulasi paginator sederhana (pakai LengthAwarePaginator)
        $page     = $request->get('page', 1);
        $perPage  = 8;
        $items    = $books->slice(($page - 1) * $perPage, $perPage)->values();
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items, $books->count(), $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('member.books.index', [
            'books'      => $paginator,
            'categories' => $this->dummyCategories(),
        ]);
    }

    // ── Detail Buku ───────────────────────────────────────
    public function show($id)
    {
        $all  = collect($this->dummyBooks());
        $book = $all->firstWhere('id', (int) $id) ?? $all->first();

        return view('member.books.show', [
            'book'      => $book,
            'dipinjam'  => 2,           // stok sedang dipinjam (dummy)
            'kuotaAktif'=> 1,           // pinjaman aktif user ini
            'maxPinjam' => 3,           // batas maks per anggota
        ]);
    }
}
