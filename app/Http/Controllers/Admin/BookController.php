<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Buku::with('kategori');
        
        if ($request->search) {
            $q = strtolower($request->search);
            $query->whereRaw("LOWER(judul) LIKE ?", ["%{$q}%"])
                  ->orWhereRaw("LOWER(penulis) LIKE ?", ["%{$q}%"]);
        }
        
        $books = $query->paginate(10);
        return view('admin.books.index', ['books' => $books]);
    }

    public function create()
    {
        $categories = Kategori::all();
        return view('admin.books.form', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'judul' => 'required|string|max:75',
            'penulis' => 'required|string|max:50',
            'penerbit' => 'required|string|max:30',
            'tahun_terbit' => 'required|integer|min:1900',
            'isbn' => 'required|unique:buku',
            'stok' => 'required|integer|min:0',
        ]);

        Buku::create($validated);

        return redirect()->route('admin.books.index')
                         ->with('success', 'Buku berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $book = Buku::findOrFail($id);
        $categories = Kategori::all();
        
        return view('admin.books.form', [
            'book' => $book,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, $id)
    {
        $book = Buku::findOrFail($id);
        
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'judul' => 'required|string|max:75',
            'penulis' => 'required|string|max:50',
            'penerbit' => 'required|string|max:30',
            'tahun_terbit' => 'required|integer|min:1900',
            'isbn' => 'required|unique:buku,isbn,' . $book->id,
            'stok' => 'required|integer|min:0',
        ]);

        $book->update($validated);

        return redirect()->route('admin.books.index')
                         ->with('success', 'Buku berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Buku::findOrFail($id)->delete();

        return redirect()->route('admin.books.index')
                         ->with('success', 'Buku berhasil dihapus!');
    }
}
