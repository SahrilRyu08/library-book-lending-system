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
        $cart = session('cart', []);

        $activeLoans = $user
            ->peminjaman()
            ->whereNull('tanggal_kembali')
            ->whereIn('status', ['menunggu', 'dipinjam', 'terlambat'])
            ->with('detail.buku')
            ->get();

        $kuotaAktif = $activeLoans->sum(fn ($loan) => $loan->detail->sum('jumlah'));
        $maxPinjam = config('library.max_buku_per_pinjam', 3);
        $cartBookIds = array_map('intval', array_keys($cart));
        $cartBookTitles = Buku::whereIn('id', $cartBookIds)
            ->pluck('judul')
            ->filter()
            ->values()
            ->all();
        $bookInCart = isset($cart[$book->id]);
        $titleInCart = in_array($book->judul, $cartBookTitles, true);
        $sudahMengajukanBukuSama = $activeLoans
            ->flatMap(fn ($loan) => $loan->detail)
            ->contains(function ($detail) use ($book) {
                return (int) $detail->buku_id === (int) $book->id
                    || $detail->buku?->judul === $book->judul;
            });

        $bisaPinjam = $book->tersedia > 0
            && $kuotaAktif < $maxPinjam
            && count($cart) < $maxPinjam
            && !$bookInCart
            && !$titleInCart
            && !$sudahMengajukanBukuSama;

        return view('member.books.show', compact(
            'book',
            'kuotaAktif',
            'maxPinjam',
            'bookInCart',
            'titleInCart',
            'sudahMengajukanBukuSama',
            'bisaPinjam'
        ));
    }
}
