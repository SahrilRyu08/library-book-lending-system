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
     * - Terlambat -> jumlah hari terlambat x tarif_denda_per_hari
     */
    public function hitungDenda(Peminjaman $peminjaman): int
    {
        if (!$peminjaman->jatuh_tempo) {
            return 0;
        }

        $tanggalAcuan = $peminjaman->tanggal_kembali
            ? Carbon::parse($peminjaman->tanggal_kembali)->startOfDay()
            : Carbon::today();

        $jatuhTempo = Carbon::parse($peminjaman->jatuh_tempo)->startOfDay();

        if ($tanggalAcuan->lte($jatuhTempo)) {
            return 0;
        }

        $hariTerlambat = $jatuhTempo->diffInDays($tanggalAcuan);

        return $hariTerlambat * config('library.tarif_denda_per_hari', 1000);
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
            ->whereNull('tanggal_kembali')
            ->whereDate('jatuh_tempo', '<', Carbon::today())
            ->update([
                'status' => 'terlambat'
            ]);
    }
}
