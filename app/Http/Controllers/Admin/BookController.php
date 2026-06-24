<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Admin BookController - DUMMY untuk preview UI
 */
class BookController extends Controller
{
    private function dummyBooks(): \Illuminate\Support\Collection
    {
        $data = [
            [1, 'Laskar Pelangi',  'Andrea Hirata',      'Fiksi',             3],
            [2, 'Bumi Manusia',    'Pramoedya A.T.',     'Fiksi',             1],
            [3, 'Filosofi Teras',  'Henry Manampiring',  'Non-Fiksi',         0],
            [4, 'Atomic Habits',   'James Clear',        'Pengembangan Diri', 5],
            [5, 'Sapiens',         'Yuval Noah Harari',  'Non-Fiksi',         2],
        ];

        return collect($data)->map(function ($d) {
            $b = new \stdClass();
            $b->id = $d[0]; $b->judul = $d[1]; $b->penulis = $d[2];
            $b->stok = $d[3]; $b->cover = null;
            $b->isbn = '978-000-' . $d[0]; $b->penerbit = 'Gramedia';
            $b->tahun_terbit = 2020 + $d[0]; $b->deskripsi = 'Deskripsi buku.';
            $b->kategori_id = $d[0];
            $kat = new \stdClass(); $kat->nama_kategori = $d[3];
            $b->kategori = $kat;
            return $b;
        });
    }

    private function dummyCategories(): \Illuminate\Support\Collection
    {
        return collect(['Fiksi', 'Non-Fiksi', 'Pengembangan Diri'])->mapWithKeys(function ($name, $i) {
            $c = new \stdClass(); $c->id = $i + 1; $c->nama_kategori = $name;
            return [$i => $c];
        })->values();
    }

    public function index(Request $request)
    {
        $books = $this->dummyBooks();
        if ($request->search) {
            $q = strtolower($request->search);
            $books = $books->filter(fn($b) => str_contains(strtolower($b->judul), $q));
        }
        $page = $request->get('page', 1);
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $books->forPage($page, 10), $books->count(), 10, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        return view('admin.books.index', ['books' => $paginator]);
    }

    public function create()
    {
        return view('admin.books.form', ['categories' => $this->dummyCategories()]);
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.books.index')
                         ->with('success', 'Buku berhasil ditambahkan! (preview dummy)');
    }

    public function edit($id)
    {
        $book = $this->dummyBooks()->firstWhere('id', (int) $id) ?? $this->dummyBooks()->first();
        return view('admin.books.form', [
            'book'       => $book,
            'categories' => $this->dummyCategories(),
        ]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.books.index')
                         ->with('success', 'Buku berhasil diperbarui! (preview dummy)');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.books.index')
                         ->with('success', 'Buku berhasil dihapus! (preview dummy)');
    }
}
