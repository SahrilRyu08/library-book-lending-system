<?php

namespace App\Console\Commands;

use App\Models\Peminjaman;
use App\Notifications\JatuhTempoNotification;
use App\Notifications\KeterlambatanNotification;
use App\Services\LoanService;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Dijalankan harian lewat scheduler (routes/console.php atau Kernel):
 *   $schedule->command('loans:check-due')->dailyAt('07:00');
 */
class CheckDueLoans extends Command
{
    protected $signature = 'loans:check-due';

    protected $description = 'Tandai peminjaman terlambat & kirim notifikasi jatuh tempo / keterlambatan';

    public function handle(LoanService $loanService): int
    {
        // 1. Tandai peminjaman yang sudah lewat jatuh_tempo jadi 'terlambat'
        $jumlahTerlambat = $loanService->tandaiTerlambat();
        $this->info("Menandai {$jumlahTerlambat} peminjaman menjadi terlambat.");

        // 2. Reminder H-3 untuk yang masih 'dipinjam'
        $hMinus = 3;
        $tanggalTarget = Carbon::now()->addDays($hMinus)->toDateString();

        $akanJatuhTempo = Peminjaman::where('status', 'dipinjam')
            ->whereDate('jatuh_tempo', $tanggalTarget)
            ->with(['user', 'detail.buku'])
            ->get();

        foreach ($akanJatuhTempo as $loan) {
            if ($loan->user) {
                $loan->user->notify(new JatuhTempoNotification($loan, $hMinus));
            }
        }
        $this->info("Mengirim {$akanJatuhTempo->count()} reminder H-{$hMinus}.");

        // 3. Notifikasi keterlambatan untuk yang baru saja jadi 'terlambat'
        $terlambat = Peminjaman::where('status', 'terlambat')
            ->with(['user', 'detail.buku'])
            ->get();

        foreach ($terlambat as $loan) {
            if ($loan->user) {
                $loan->user->notify(new KeterlambatanNotification($loan));
            }
        }
        $this->info("Mengirim {$terlambat->count()} notifikasi keterlambatan.");

        return self::SUCCESS;
    }
}
