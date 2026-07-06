<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use App\Models\User;
use App\Notifications\PengajuanPeminjamanBaruNotification;
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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $aktifLoans = $user
            ->peminjaman()
            ->with('detail.buku')
            ->whereNull('tanggal_kembali')
            ->whereIn('status', ['menunggu', 'dipinjam', 'terlambat'])
            ->latest()
            ->get();

        $maxPinjam  = config('library.max_buku_per_pinjam', 3);
        $aktif      = $aktifLoans->sum(fn ($loan) => $loan->detail->sum('jumlah'));

        return view('member.loans.index', compact('aktifLoans', 'aktif', 'maxPinjam'));
    }

    /**
     * Proses ajukan peminjaman dari keranjang
     * Status awal = 'menunggu' — menunggu konfirmasi admin
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $cart      = session('cart', []);
        $maxPinjam = config('library.max_buku_per_pinjam', 3);

        // Keranjang kosong
        if (empty($cart)) {
            return redirect()->route('member.cart.index')
                ->with('error', 'Keranjang kosong. Tambahkan buku terlebih dahulu.');
        }

        // Hitung kuota aktif
        $activeLoans = $user
            ->peminjaman()
            ->whereNull('tanggal_kembali')
            ->whereIn('status', ['menunggu', 'dipinjam', 'terlambat'])
            ->with('detail.buku')
            ->get();
        $kuotaAktif = $activeLoans->sum(fn ($loan) => $loan->detail->sum('jumlah'));

        if (($kuotaAktif + count($cart)) > $maxPinjam) {
            return redirect()->route('member.cart.index')
                ->with('error', 'Kuota peminjaman tidak mencukupi.');
        }

        DB::beginTransaction();
        try {
            $jatuhTempo = now()->addDays(config('library.max_hari_pinjam', 7));

            $activeBookIds = $activeLoans
                ->flatMap(fn ($loan) => $loan->detail->pluck('buku_id'))
                ->map(fn ($id) => (int) $id)
                ->all();
            $activeBookTitles = $activeLoans
                ->flatMap(fn ($loan) => $loan->detail->pluck('buku.judul'))
                ->filter()
                ->values()
                ->all();
            $judulDiKeranjang = Buku::whereIn('id', array_keys($cart))
                ->pluck('judul')
                ->filter()
                ->values()
                ->all();

            if (count($judulDiKeranjang) !== count(array_unique($judulDiKeranjang))) {
                DB::rollBack();
                return redirect()->route('member.cart.index')
                    ->with('error', 'Keranjang berisi judul buku yang sama. Gunakan buku yang berbeda.');
            }

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
                        ->with('error', 'Stok "' . $buku->judul . '" tidak mencukupi saat pengajuan.');
                }

                if (
                    in_array((int) $bukuId, $activeBookIds, true)
                    || in_array($buku->judul, $activeBookTitles, true)
                ) {
                    DB::rollBack();
                    return redirect()->route('member.cart.index')
                        ->with('error', 'User tidak bisa meminjam buku dengan ID atau judul yang sama.');
                }

                PeminjamanDetail::create([
                    'peminjaman_id' => $peminjaman->id,
                    'buku_id'       => $bukuId,
                    'jumlah'        => 1,
                ]);
            }

            DB::commit();

// Muat ulang relasi yang dibutuhkan notifikasi
            $peminjaman->load(['user', 'detail.buku']);

// Kirim notifikasi ke semua admin
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new PengajuanPeminjamanBaruNotification($peminjaman));
            }

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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $historyLoans = $user
            ->peminjaman()
            ->with('detail.buku')
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
