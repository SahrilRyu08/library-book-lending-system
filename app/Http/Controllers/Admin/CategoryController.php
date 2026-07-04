<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Kategori::withCount('buku')->get();
        return view('admin.categories.index', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|unique:kategori',
            'deskripsi' => 'nullable',
        ]);

        Kategori::create($validated);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);
        
        $validated = $request->validate([
            'nama' => 'required|unique:kategori,nama,' . $kategori->id,
            'deskripsi' => 'nullable',
        ]);

        $kategori->update($validated);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Kategori::findOrFail($id)->delete();

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Kategori berhasil dihapus!');
    }
}
