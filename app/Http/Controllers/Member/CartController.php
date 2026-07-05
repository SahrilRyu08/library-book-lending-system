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
     * Format: [buku_id => jumlah, ...]
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
            'jumlah'  => ['nullable', 'integer', 'min:1'],
        ]);

        $buku      = Buku::findOrFail($request->buku_id);
        $jumlah    = (int) $request->input('jumlah', 1);
        $maxPinjam = config('library.max_buku_per_pinjam', 3);
        $cart      = $this->getCart();
        $jumlahSaatIni = (int) ($cart[$request->buku_id] ?? 0);

        // Hitung total slot yang sudah terpakai
        $totalDiKeranjang = array_sum($cart);
        $kuotaAktif       = $user
            ->peminjaman()
            ->whereNull('tanggal_kembali')
            ->whereIn('status', ['menunggu', 'dipinjam', 'terlambat'])
            ->with('detail')
            ->get()
            ->sum(fn ($loan) => $loan->detail->sum('jumlah'));

        // Cek kuota
        if (($kuotaAktif + $totalDiKeranjang + $jumlah) > $maxPinjam) {
            return back()->with('error',
                'Kuota penuh. Maksimal ' . $maxPinjam . ' buku aktif per anggota.'
            );
        }

        // Cek stok tersedia
        if ($buku->tersedia < 1) {
            return back()->with('error', 'Stok buku "' . $buku->judul . '" sedang tidak tersedia.');
        }

        if (($jumlahSaatIni + $jumlah) > $buku->tersedia) {
            return back()->with('error',
                'Jumlah "' . $buku->judul . '" di keranjang melebihi stok tersedia.'
            );
        }

        // Tambah/increment jumlah buku di keranjang
        $cart[$request->buku_id] = $jumlahSaatIni + $jumlah;
        $this->saveCart($cart);

        return back()->with(
            'success',
            '"' . $buku->judul . '" berhasil ditambahkan ke keranjang sejumlah ' . $jumlah . ' buku.'
        );
    }

    /**
     * Ubah jumlah buku pada keranjang
     */
    public function update(Request $request, $bukuId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $this->getCart();

        if (!isset($cart[$bukuId])) {
            return back()->with('error', 'Buku tidak ditemukan di keranjang.');
        }

        $buku      = Buku::findOrFail($bukuId);
        $jumlah    = (int) $request->input('jumlah');
        $maxPinjam = config('library.max_buku_per_pinjam', 3);
        $totalLain = array_sum($cart) - (int) $cart[$bukuId];
        $kuotaAktif = $user
            ->peminjaman()
            ->whereNull('tanggal_kembali')
            ->whereIn('status', ['menunggu', 'dipinjam', 'terlambat'])
            ->with('detail')
            ->get()
            ->sum(fn ($loan) => $loan->detail->sum('jumlah'));

        if (($kuotaAktif + $totalLain + $jumlah) > $maxPinjam) {
            return back()->with('error',
                'Kuota penuh. Maksimal ' . $maxPinjam . ' buku aktif per anggota.'
            );
        }

        if ($jumlah > $buku->tersedia) {
            return back()->with('error',
                'Jumlah "' . $buku->judul . '" melebihi stok tersedia.'
            );
        }

        $cart[$bukuId] = $jumlah;
        $this->saveCart($cart);

        return back()->with('success', 'Jumlah buku di keranjang berhasil diperbarui.');
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
