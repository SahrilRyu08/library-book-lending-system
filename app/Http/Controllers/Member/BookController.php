<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\PeminjamanDetail;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Buku::with('kategori');

        $query->when($request->search, function ($q) use ($request) {
            $q->where('judul', 'LIKE', '%' . $request->search . '%')
              ->orWhere('penulis', 'LIKE', '%' . $request->search . '%');
        });

        $query->when($request->category, function ($q) use ($request) {
            $q->where('kategori_id', $request->category);
        });

        $buku = $query->paginate(12)->withQueryString();

        $categories = Kategori::all();

        $buku->each(function ($item) {
            $sedangDipinjam = PeminjamanDetail::where('buku_id', $item->id)
                                ->whereHas('peminjaman', function($q) {
                                    $q->where('status', 'dipinjam');
                                })->sum('jumlah');

            $item->stok_tersedia = $item->stok - $sedangDipinjam;
        });

        return view('member.books.index', ['books'=> $buku, 'categories' => $categories]);
    }

    public function show($id)
    {
        $buku = Buku::with('kategori')->findOrFail($id);

        $sedangDipinjam = PeminjamanDetail::where('buku_id', $buku->id)
                            ->whereHas('peminjaman', function($q) {
                                $q->where('status', 'dipinjam');
                            })->sum('jumlah');

        $buku->stok_tersedia = $buku->stok - $sedangDipinjam;

        $kuotaAktif = 0;
        $maxPinjam = 3;

        return view('member.books.show', ['book' => $buku, 'kuotaAktif' => $kuotaAktif, 'maxPinjam' => $maxPinjam]);
    }
}
