<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    /**
     * Tampilkan katalog buku dengan search dan filter kategori
     */
    public function index(Request $request)
    {
        $query = Buku::with('kategori');

        // Filter pencarian judul atau penulis
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('penulis', 'like', '%' . $request->search . '%');
            });
        }

        // Filter kategori
        if ($request->category) {
            $query->where('kategori_id', $request->category);
        }

        $books      = $query->paginate(8)->withQueryString();
        $categories = Kategori::orderBy('nama_kategori')->get();

        return view('member.books.index', compact('books', 'categories'));
    }

    /**
     * Tampilkan detail satu buku
     * Hitung kuota aktif user yang sedang login
     */
    public function show($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $book = Buku::with('kategori')->findOrFail($id);

        // Hitung berapa buku yang sedang dipinjam user ini
        $kuotaAktif = $user
            ->peminjaman()
            ->whereNull('tanggal_kembali')
            ->whereIn('status', ['menunggu', 'dipinjam', 'terlambat'])
            ->with('detail')
            ->get()
            ->sum(fn ($loan) => $loan->detail->sum('jumlah'));

        $maxPinjam = config('library.max_buku_per_pinjam', 3);

        return view('member.books.show', compact('book', 'kuotaAktif', 'maxPinjam'));
    }
}
