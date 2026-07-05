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
     * Format: [buku_id => 1, ...]  — jumlah selalu 1 per judul
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
        $cart       = $this->getCart();
        $maxPinjam  = config('library.max_buku_per_pinjam', 3);
        $kuotaAktif = Auth::user()
            ->peminjaman()
            ->whereIn('status', ['menunggu', 'dipinjam'])
            ->count();

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
     * Jumlah selalu 1 per judul buku
     */
    public function store(Request $request)
    {
        $request->validate([
            'buku_id' => ['required', 'integer', 'exists:buku,id'],
        ]);

        $buku      = Buku::findOrFail($request->buku_id);
        $maxPinjam = config('library.max_buku_per_pinjam', 3);
        $cart      = $this->getCart();

        // Cek buku sudah ada di keranjang
        if (isset($cart[$request->buku_id])) {
            return back()->with('error', '"' . $buku->judul . '" sudah ada di keranjang.');
        }

        // Hitung total slot yang sudah terpakai
        $totalDiKeranjang = count($cart);
        $kuotaAktif       = Auth::user()
            ->peminjaman()
            ->whereIn('status', ['menunggu', 'dipinjam'])
            ->count();

        // Cek kuota
        if (($kuotaAktif + $totalDiKeranjang + 1) > $maxPinjam) {
            return back()->with('error',
                'Kuota penuh. Maksimal ' . $maxPinjam . ' buku aktif per anggota.'
            );
        }

        // Cek stok tersedia
        if ($buku->tersedia < 1) {
            return back()->with('error', 'Stok buku "' . $buku->judul . '" sedang tidak tersedia.');
        }

        // Tambah ke keranjang dengan jumlah 1
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
