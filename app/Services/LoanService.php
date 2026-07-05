<?php

namespace App\Services;

use App\Models\Peminjaman;
use Carbon\Carbon;

/**
 * Kontrak untuk Dev 5 (returns-reports-dashboard):
 * - hitungDenda(Peminjaman $p): int
 * - tandaiTerlambat(): int  (jumlah baris yang diupdate)
 *
 * Dipakai oleh: scheduler, reminder jatuh tempo, notifikasi
 * keterlambatan, dan laporan denda.
 */
class LoanService
{
    /**
     * Hitung estimasi denda untuk satu peminjaman berdasarkan jatuh_tempo.
     * - Belum terlambat -> 0
     * - Terlambat -> jumlah hari terlambat x denda_per_hari
     */
    public function hitungDenda(Peminjaman $peminjaman): int
    {
        if (!$peminjaman->jatuh_tempo) {
            return 0;
        }

        $today      = Carbon::now()->startOfDay();
        $jatuhTempo = Carbon::parse($peminjaman->jatuh_tempo)->startOfDay();

        if ($today->lte($jatuhTempo)) {
            return 0;
        }

        $hariTerlambat = $jatuhTempo->diffInDays($today);
        $dendaPerHari  = (int) config('library.denda_per_hari', 1000);

        return $hariTerlambat * $dendaPerHari;
    }

    /**
     * Tandai seluruh peminjaman yang statusnya masih 'dipinjam'
     * tapi sudah lewat jatuh_tempo menjadi 'terlambat'.
     * Cocok dipanggil dari scheduler harian.
     *
     * @return int jumlah baris yang diupdate
     */
    public function tandaiTerlambat(): int
    {
        return Peminjaman::where('status', 'dipinjam')
            ->whereDate('jatuh_tempo', '<', Carbon::now()->toDateString())
            ->update(['status' => 'terlambat']);
    }
}
