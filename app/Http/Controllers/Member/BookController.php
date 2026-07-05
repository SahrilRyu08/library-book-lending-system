<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function index()
    {
        $categories = Kategori::all();
        $query = Buku::query();

        if (request()->has('search') && request()->get('search') != '') {
            $searchTerm = request()->get('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('judul', 'like', '%' . $searchTerm . '%')
                  ->orWhere('penulis', 'like', '%' . $searchTerm . '%');
            });
        }

        if (request()->has('category') && request()->get('category') != '') {
            $query->where('kategori_id', request()->get('category'));
        }

        $books = $query->paginate(10);

        $books->each(function ($buku) {
            $totalDipinjam = DB::table('peminjaman_detail')
                ->join('peminjaman', 'peminjaman_detail.peminjaman_id', '=', 'peminjaman.id')
                ->where('peminjaman_detail.buku_id', $buku->id)
                ->whereIn('peminjaman.status', ['dipinjam', 'terlambat'])
                ->sum('peminjaman_detail.jumlah');

            $buku->sisa_stok = max(0, $buku->stok - $totalDipinjam);
        });

        return view('member.books.index', compact('books', 'categories'));
    }

    public function show($id)
    {
        $book = Buku::findOrFail($id);

        $totalDipinjam = DB::table('peminjaman_detail')
            ->join('peminjaman', 'peminjaman_detail.peminjaman_id', '=', 'peminjaman.id')
            ->where('peminjaman_detail.buku_id', $book->id)
            ->whereIn('peminjaman.status', ['dipinjam', 'terlambat'])
            ->sum('peminjaman_detail.jumlah');

        $book->sisa_stok = max(0, $book->stok - $totalDipinjam);

        $kuotaAktif = DB::table('peminjaman_detail')
            ->join('peminjaman', 'peminjaman_detail.peminjaman_id', '=', 'peminjaman.id')
            ->where('peminjaman.user_id', auth()->id())
            ->whereIn('peminjaman.status', ['dipinjam', 'terlambat'])
            ->sum('peminjaman_detail.jumlah');

        $maxPinjam = 3;

        return view('member.books.show', compact('book', 'kuotaAktif', 'maxPinjam'));
    }
}
