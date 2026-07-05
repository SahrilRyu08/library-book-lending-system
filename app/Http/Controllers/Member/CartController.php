<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Ambil isi keranjang dari session
     * Format: [buku_id => 1]
     */
    private function getCart(): array
    {
        return session('cart', []);
    }

    private function saveCart(array $cart): void
    {
        session(['cart' => $cart]);
    }

    /**
     * Tampilkan halaman keranjang
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $cart       = $this->getCart();
        $maxPinjam  = config('library.max_buku_per_pinjam', 3);
        $kuotaAktif = $user
            ->peminjaman()
            ->whereNull('tanggal_kembali')
            ->whereIn('status', ['menunggu', 'dipinjam', 'terlambat'])
            ->with('detail')
            ->get()
            ->sum(fn ($loan) => $loan->detail->sum('jumlah'));

        $bukuIds = array_keys($cart);
        $books   = Buku::with('kategori')
                       ->whereIn('id', $bukuIds)
                       ->get()
                       ->keyBy('id');

        return view('member.cart.index', compact(
            'cart', 'books', 'kuotaAktif', 'maxPinjam'
        ));
    }

    /**
     * Tambah buku ke keranjang
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'buku_id' => ['required', 'integer', 'exists:buku,id'],
        ]);

        $buku      = Buku::findOrFail($request->buku_id);
        $maxPinjam = config('library.max_buku_per_pinjam', 3);
        $cart      = $this->getCart();
        $kuotaAktif = $user
            ->peminjaman()
            ->whereNull('tanggal_kembali')
            ->whereIn('status', ['menunggu', 'dipinjam', 'terlambat'])
            ->with('detail.buku')
            ->get();

        $kuotaAktif = $kuotaAktif->sum(fn ($loan) => $loan->detail->sum('jumlah'));
        $activeBookIds = $user
            ->peminjaman()
            ->whereNull('tanggal_kembali')
            ->whereIn('status', ['menunggu', 'dipinjam', 'terlambat'])
            ->with('detail')
            ->get()
            ->flatMap(fn ($loan) => $loan->detail->pluck('buku_id'))
            ->map(fn ($id) => (int) $id)
            ->all();
        $activeBookTitles = $user
            ->peminjaman()
            ->whereNull('tanggal_kembali')
            ->whereIn('status', ['menunggu', 'dipinjam', 'terlambat'])
            ->with('detail.buku')
            ->get()
            ->flatMap(fn ($loan) => $loan->detail->pluck('buku.judul'))
            ->filter()
            ->values()
            ->all();
        $cartBookIds = array_map('intval', array_keys($cart));
        $cartBookTitles = Buku::whereIn('id', $cartBookIds)
            ->pluck('judul')
            ->filter()
            ->values()
            ->all();

        if (
            in_array($buku->id, $activeBookIds, true)
            || in_array($buku->judul, $activeBookTitles, true)
            || in_array($buku->id, $cartBookIds, true)
            || in_array($buku->judul, $cartBookTitles, true)
        ) {
            return back()->with('error', 'User tidak bisa meminjam buku dengan ID atau judul yang sama.');
        }

        if (($kuotaAktif + count($cart)) >= $maxPinjam) {
            return back()->with('error',
                'Maksimal peminjaman adalah 3 buku berbeda per user.'
            );
        }

        // Cek stok tersedia
        if ($buku->tersedia < 1) {
            return back()->with('error', 'Stok buku "' . $buku->judul . '" sedang tidak tersedia.');
        }

        $cart[$request->buku_id] = 1;
        $this->saveCart($cart);

        return back()->with('success', '"' . $buku->judul . '" berhasil ditambahkan ke keranjang.');
    }

    /**
     * Hapus buku dari keranjang
     */
    public function destroy($bukuId)
    {
        $cart = $this->getCart();
        unset($cart[$bukuId]);
        $this->saveCart($cart);

        return back()->with('success', 'Buku berhasil dihapus dari keranjang.');
    }
}
