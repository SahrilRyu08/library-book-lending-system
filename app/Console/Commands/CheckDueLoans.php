<?php

namespace App\Console\Commands;

use App\Notifications\JatuhTempoNotification;
use App\Notifications\KeterlambatanNotification;
use App\Services\LoanService;
use Illuminate\Console\Command;

class CheckDueLoans extends Command
{
    protected $signature = 'loans:check-due';

    protected $description = 'Cek peminjaman jatuh tempo H-3 dan tandai peminjaman yang sudah terlambat';

    public function handle(LoanService $loanService): int
    {
        $this->handleReminder($loanService);
        $this->handleTerlambat($loanService);

        return self::SUCCESS;
    }

    /**
     * Kirim reminder H-minus sesuai config('library.notif_h_minus').
     */
    protected function handleReminder(LoanService $loanService): void
    {
        $hMinus = (int) config('library.notif_h_minus', 3);

        $items = $loanService->ambilJatuhTempoMendekati($hMinus);

        foreach ($items as $peminjaman) {
            // Cek supaya tidak kirim notif ganda di hari yang sama
            $sudahDikirim = $peminjaman->user->notifications()
                ->where('type', JatuhTempoNotification::class)
                ->whereDate('created_at', now()->toDateString())
                ->where('data->peminjaman_id', $peminjaman->id)
                ->exists();

            if (!$sudahDikirim) {
                $peminjaman->user->notify(
                    new JatuhTempoNotification($peminjaman, $hMinus)
                );
            }
        }

        $this->info("Reminder H-{$hMinus} terkirim ke {$items->count()} peminjaman.");
    }

    /**
     * Tandai peminjaman yang lewat jatuh tempo sebagai terlambat,
     * lalu kirim notifikasi HANYA untuk yang baru saja berubah status
     * (supaya tidak mengirim notifikasi ganda tiap hari).
     */
    protected function handleTerlambat(LoanService $loanService): void
    {
        $baruTerlambat = $loanService->tandaiTerlambat();

        foreach ($baruTerlambat as $peminjaman) {
            $peminjaman->user->notify(new KeterlambatanNotification($peminjaman));
        }

        $this->info("{$baruTerlambat->count()} peminjaman baru ditandai terlambat.");
    }
}
