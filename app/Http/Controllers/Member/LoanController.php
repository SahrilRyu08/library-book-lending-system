<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use App\Services\LoanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    /**
     * Peminjaman aktif milik user yang sedang login
     * Status menunggu dan dipinjam sama-sama ditampilkan di sini
     */
    public function index()
    {
        $aktifLoans = Auth::user()
            ->peminjaman()
            ->with('detail.buku')
            ->whereIn('status', ['menunggu', 'dipinjam'])
            ->latest()
            ->get();

        $maxPinjam  = config('library.max_buku_per_pinjam', 3);
        $aktif      = $aktifLoans->count();

        return view('member.loans.index', compact('aktifLoans', 'aktif', 'maxPinjam'));
    }

    /**
     * Proses ajukan peminjaman dari keranjang
     * Status awal = 'menunggu' — menunggu konfirmasi admin
     */
    public function store(Request $request)
    {
        $cart      = session('cart', []);
        $maxPinjam = config('library.max_buku_per_pinjam', 3);

        // Keranjang kosong
        if (empty($cart)) {
            return redirect()->route('member.cart.index')
                ->with('error', 'Keranjang kosong. Tambahkan buku terlebih dahulu.');
        }

        // Hitung kuota aktif
        $kuotaAktif = Auth::user()
            ->peminjaman()
            ->whereIn('status', ['menunggu', 'dipinjam'])
            ->count();

        if (($kuotaAktif + count($cart)) > $maxPinjam) {
            return redirect()->route('member.cart.index')
                ->with('error', 'Kuota peminjaman tidak mencukupi.');
        }

        DB::beginTransaction();
        try {
            $jatuhTempo = now()->addDays(config('library.max_hari_pinjam', 7));

            // Buat 1 header peminjaman
            $peminjaman = Peminjaman::create([
                'user_id'       => Auth::id(),
                'tanggal_pinjam'=> now()->toDateString(),
                'jatuh_tempo'   => $jatuhTempo->toDateString(),
                'status'        => 'menunggu', // menunggu konfirmasi admin
                'denda'         => 0,
            ]);

            // Buat detail per buku di keranjang
            foreach ($cart as $bukuId => $jumlah) {
                $buku = Buku::findOrFail($bukuId);

                // Validasi stok sekali lagi sebelum simpan
                if ($buku->tersedia < 1) {
                    DB::rollBack();
                    return redirect()->route('member.cart.index')
                        ->with('error', 'Stok "' . $buku->judul . '" habis saat pengajuan.');
                }

                PeminjamanDetail::create([
                    'peminjaman_id' => $peminjaman->id,
                    'buku_id'       => $bukuId,
                    'jumlah'        => 1,
                ]);
            }

            DB::commit();

            // Kosongkan keranjang setelah berhasil
            session()->forget('cart');

            return redirect()->route('member.loans.index')
                ->with('success', 'Permintaan peminjaman berhasil diajukan! Menunggu konfirmasi admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('member.cart.index')
                ->with('error', 'Terjadi kesalahan saat mengajukan peminjaman. Coba lagi.');
        }
    }

    /**
     * Riwayat peminjaman yang sudah selesai
     */
    public function history()
    {
        $historyLoans = Auth::user()
            ->peminjaman()
            ->with('detail.buku')
            ->whereIn('status', ['selesai', 'terlambat'])
            ->latest()
            ->paginate(10);

        return view('member.loans.history', compact('historyLoans'));
    }

    /**
     * Detail satu transaksi peminjaman
     */

    public function show(Peminjaman $loan, LoanService $loanService)
    {
        $estimasiDenda = $loanService->hitungDenda($loan);

        return view('member.loans.show', compact('loan', 'estimasiDenda'));
    }
}
