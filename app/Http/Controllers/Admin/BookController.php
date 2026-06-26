<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Support\Facades\Storage;

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
        //dd(Buku::count());
        $books = Buku::with('kategori');

        if ($request->filled('search')) {
            $search = $request->search;

            $books->where(function ($query) use ($search) {
                $query->where('judul', 'like', "%{$search}%")
                    ->orWhere('penulis', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        $books = $books->orderBy('judul')
                    ->paginate(10)
                    ->withQueryString();

        return view('admin.books.index', [
            'books' => $books
        ]);
    }

    public function create()
    {
        $categories = Kategori::all();

        return view('admin.books.form', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'         => 'required|max:75',
            'penulis'       => 'required|max:50',
            'penerbit'      => 'required|max:30',
            'tahun_terbit'  => 'required|digits:4',
            'isbn'          => 'required|max:20|unique:buku,isbn',
            'kategori_id'   => 'required|exists:kategori,id',
            'stok'          => 'required|integer|min:0',
            'deskripsi'     => 'nullable',
            'cover'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('cover')) {
            $validated['cover'] = $request->file('cover')->store('covers', 'public');
        }

        Buku::create($validated);

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Buku berhasil ditambahkan.');
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
