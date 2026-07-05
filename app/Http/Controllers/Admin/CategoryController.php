<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kategori;

/**
 * Admin CategoryController - DUMMY untuk preview UI
 */
class CategoryController extends Controller
{
    private function dummyCategories(): \Illuminate\Support\Collection
    {
        $data = [
            [1, 'Fiksi',             'Novel, cerpen, dan karya fiksi lainnya', 12],
            [2, 'Non-Fiksi',         'Buku berbasis fakta dan referensi',       8],
            [3, 'Pengembangan Diri', 'Self-help dan produktivitas',             5],
        ];

        return collect($data)->map(function ($d) {
            $c = new \stdClass();
            $c->id           = $d[0];
            $c->nama_kategori = $d[1];
            $c->deskripsi    = $d[2];
            $c->buku_count   = $d[3];
            return $c;
        });
    }

    public function index()
    {
        $categories = Kategori::withCount('buku')
                        ->orderBy('nama_kategori')
                        ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|max:30|unique:kategori,nama_kategori',
            'deskripsi'     => 'nullable',
        ]);

        kategori::create($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $category = kategori::findOrFail($id);

        $validated = $request->validate([
            'nama_kategori' => 'required|max:30|unique:kategori,nama_kategori,' . $category->id,
            'deskripsi'     => 'nullable',
        ]);

        $category->update($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $category = kategori::findOrFail($id);

        if ($category->buku()->count() > 0) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh buku.');
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
