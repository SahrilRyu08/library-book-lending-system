<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori; // 1. Pastikan model Kategori di-import
use App\Models\PeminjamanDetail;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        // 2. Ambil data buku
        $buku = Buku::with('kategori')->paginate(12);

        // 3. Ambil data kategori untuk dropdown filter
        $categories = Kategori::all();

        // 4. Hitung stok tersedia
        $buku->each(function ($item) {
            $sedangDipinjam = PeminjamanDetail::where('buku_id', $item->id)
                                ->whereHas('peminjaman', function($q) {
                                    $q->where('status', 'dipinjam');
                                })->sum('jumlah');

            $item->stok_tersedia = $item->stok - $sedangDipinjam;
        });

        // 5. Kirim KEDUANYA ke view
        return view('member.books.index', ['books'=> $buku, 'categories' => $categories]);
    }
    public function show($id)
        {
            // Mencari buku berdasarkan ID, jika tidak ketemu akan error 404
            $buku = Buku::with('kategori')->findOrFail($id);

            // Menghitung stok tersedia untuk buku spesifik ini
            $sedangDipinjam = PeminjamanDetail::where('buku_id', $buku->id)
                                ->whereHas('peminjaman', function($q) {
                                    $q->where('status', 'dipinjam');
                                })->sum('jumlah');

            $buku->stok_tersedia = $buku->stok - $sedangDipinjam;

            // Tambahkab 2 variabel ini saja
            $kuotaAktif = 0;
            $maxPinjam = 3;

            // Mengirim data ke view detail (pastikan file ini ada)
            return view('member.books.show', ['book' => $buku, 'kuotaAktif' => $kuotaAktif, 'maxPinjam' => $maxPinjam]);
        }
}
