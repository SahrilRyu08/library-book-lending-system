<?php

namespace App\Services;

use App\Models\Peminjaman;
use Carbon\Carbon;

class LoanService
{
    /**
     * Hitung denda untuk satu transaksi peminjaman.
     *
     * Aturan:
     * - Kalau belum jatuh tempo (atau tepat waktu), denda = 0.
     * - Kalau telat, denda = jumlah_hari_telat x config('library.denda_per_hari').
     * - Referensi tanggal "sekarang" pakai tanggal_kembali kalau sudah diisi
     *   (transaksi historis), atau now() kalau belum (transaksi masih berjalan).
     */
    public function hitungDenda(Peminjaman $peminjaman): int
    {
        if (!$peminjaman->jatuh_tempo) {
            return 0;
        }

        $acuanTanggal = $peminjaman->tanggal_kembali
            ? Carbon::parse($peminjaman->tanggal_kembali)
            : now();

        $jatuhTempo = Carbon::parse($peminjaman->jatuh_tempo);

        if ($acuanTanggal->lte($jatuhTempo)) {
            return 0;
        }

        $hariTelat   = $jatuhTempo->diffInDays($acuanTanggal);
        $dendaPerHari = (int) config('library.denda_per_hari', 1000);

        return $hariTelat * $dendaPerHari;
    }

    /**
     * Tandai semua peminjaman aktif yang sudah lewat jatuh tempo
     * sebagai 'terlambat'. Dipanggil oleh scheduler harian.
     *
     * Return: koleksi Peminjaman yang baru saja ditandai terlambat
     * (dipakai buat kirim notifikasi, supaya tidak double-notif
     * ke transaksi yang sudah lama berstatus terlambat).
     */
    public function tandaiTerlambat()
    {
        $query = Peminjaman::where('status', 'dipinjam')
            ->whereNotNull('jatuh_tempo')
            ->where('jatuh_tempo', '<', now())
            ->whereNull('tanggal_kembali');

        $items = $query->get();

        Peminjaman::whereIn('id', $items->pluck('id'))
            ->update(['status' => 'terlambat']);

        return $items;
    }

    /**
     * Ambil peminjaman yang jatuh tempo-nya H-minus sekian hari dari
     * sekarang, dan belum pernah dikirimi reminder (dipinjam saja,
     * belum dikembalikan). Dipakai untuk notifikasi H-3.
     */
    public function ambilJatuhTempoMendekati(int $hMinus)
    {
        $targetTanggal = now()->addDays($hMinus)->toDateString();

        return Peminjaman::whereIn('status', ['dipinjam', 'terlambat'])
            ->whereNull('tanggal_kembali')
            ->whereDate('jatuh_tempo', $targetTanggal)
            ->get();
    }
}
