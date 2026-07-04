<?php

namespace App\Services;

use App\Models\Peminjaman;
use Carbon\Carbon;

class LoanService
{
    public function hitungDenda(Peminjaman $loan): int
    {
        $tarif = (int) config('library.tarif_denda_per_hari', 1000);

        if ($loan->status !== 'selesai' || !$loan->tanggal_kembali || !$loan->jatuh_tempo) {
            // For active loans we don't persist denda here; admin/member show can estimate separately.
            return (int) ($loan->denda ?? 0);
        }

        $kembali = Carbon::parse($loan->tanggal_kembali);
        $tempo = Carbon::parse($loan->jatuh_tempo);

        if ($kembali->lte($tempo)) {
            return 0;
        }

        $hariTerlambat = $kembali->diffInDays($tempo);
        return (int) ($hariTerlambat * $tarif);
    }

    /**
     * Mark loans as overdue automatically.
     */
    public function tandaiTerlambat(): void
    {
        $tarif = (int) config('library.tarif_denda_per_hari', 1000);
        $now = Carbon::now();

        Peminjaman::query()
            ->where('status', 'dipinjam')
            ->whereDate('jatuh_tempo', '<', $now)
            ->chunkById(100, function ($loans) use ($now, $tarif) {
                foreach ($loans as $loan) {
                    $tempo = Carbon::parse($loan->jatuh_tempo);
                    $hariTerlambat = $tempo->diffInDays($now);

                    $denda = (int) ($hariTerlambat * $tarif);

                    $loan->update([
                        'status' => 'terlambat',
                        'denda' => $denda,
                    ]);
                }
            });
    }
}

