<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use App\Models\User;
use App\Services\LoanService;
use Illuminate\Database\Seeder;

class NotificationPeminjamanSeeder extends Seeder
{
    public function run(): void
    {
        $loanService = app(LoanService::class);
        $lamaPeminjaman = (int) config('library.max_hari_pinjam', 7);
        $hMinus = (int) config('library.notif_h_minus', 3);

        $anggota = User::where('role', 'anggota')->take(5)->get();
        $bukus = Buku::take(6)->get();

        if ($anggota->count() < 5 || $bukus->count() < 6) {
            $this->command->warn('Minimal dibutuhkan 5 anggota dan 6 buku.');
            return;
        }

        // 1. Selesai tepat waktu
        $this->buatPeminjaman(
            user: $anggota[0],
            buku: $bukus[0],
            tanggalPinjam: now()->subDays(10),
            jatuhTempo: now()->subDays(10)->addDays($lamaPeminjaman),
            tanggalKembali: now()->subDays(5),
            loanService: $loanService
        );

        // 2. Selesai terlambat
        $this->buatPeminjaman(
            user: $anggota[1],
            buku: $bukus[1],
            tanggalPinjam: now()->subDays(15),
            jatuhTempo: now()->subDays(15)->addDays($lamaPeminjaman),
            tanggalKembali: now()->subDays(1),
            loanService: $loanService
        );

        // 3. Masih dipinjam
        $this->buatPeminjaman(
            user: $anggota[2],
            buku: $bukus[2],
            tanggalPinjam: now()->subDays(2),
            jatuhTempo: now()->subDays(2)->addDays($lamaPeminjaman),
            tanggalKembali: null,
            loanService: $loanService
        );

        // 4. Lewat jatuh tempo tetapi masih status dipinjam
        // (untuk testing scheduler loans:check-due)
        $this->buatPeminjaman(
            user: $anggota[3],
            buku: $bukus[3],
            tanggalPinjam: now()->subDays($lamaPeminjaman + 5),
            jatuhTempo: now()->subDays(5),
            tanggalKembali: null,
            loanService: $loanService,
            statusManual: 'dipinjam'
        );

        // 5. H-3 jatuh tempo
        $this->buatPeminjaman(
            user: $anggota[4],
            buku: $bukus[4],
            tanggalPinjam: now()->subDays($lamaPeminjaman - $hMinus),
            jatuhTempo: now()->addDays($hMinus),
            tanggalKembali: null,
            loanService: $loanService
        );

        // 6. Sudah terlambat
        $this->buatPeminjaman(
            user: $anggota[0],
            buku: $bukus[5],
            tanggalPinjam: now()->subDays($lamaPeminjaman + 10),
            jatuhTempo: now()->subDays(10),
            tanggalKembali: null,
            loanService: $loanService,
            statusManual: 'terlambat'
        );

        $this->command->info('NotificationPeminjamanSeeder berhasil membuat 6 skenario peminjaman.');
    }

    protected function buatPeminjaman(
        User $user,
        Buku $buku,
             $tanggalPinjam,
             $jatuhTempo,
             $tanggalKembali,
        LoanService $loanService,
        ?string $statusManual = null
    ): Peminjaman {
        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'tanggal_pinjam' => $tanggalPinjam,
            'jatuh_tempo' => $jatuhTempo,
            'tanggal_kembali' => $tanggalKembali,
            'status' => 'dipinjam',
            'denda' => 0,
        ]);

        PeminjamanDetail::create([
            'peminjaman_id' => $peminjaman->id,
            'buku_id' => $buku->id,
            'jumlah' => 1,
        ]);

        // Sesuaikan dengan nama relasi pada model
        $peminjaman->load('detail.buku');

        if ($statusManual !== null) {
            $peminjaman->status = $statusManual;
        } elseif ($tanggalKembali !== null) {
            $peminjaman->denda = $loanService->hitungDenda($peminjaman);
            $peminjaman->status = 'selesai';
        } else {
            $peminjaman->status = 'dipinjam';
        }

        $peminjaman->save();

        if ($tanggalKembali === null) {
            $buku->decrement('stok');
        }

        return $peminjaman;
    }
}
