<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        return view('admin.categories.index', [
            'categories' => $this->dummyCategories(),
        ]);
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.categories.index')
                         ->with('success', 'Kategori berhasil ditambahkan! (preview dummy)');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.categories.index')
                         ->with('success', 'Kategori berhasil diperbarui! (preview dummy)');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.categories.index')
                         ->with('success', 'Kategori berhasil dihapus! (preview dummy)');
    }
}
