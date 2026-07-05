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
        $lamaPeminjaman = (int) config('library.lama_peminjaman', 7);

        $anggota = User::where('role', 'anggota')->get();
        $bukus   = Buku::all();

        if ($anggota->isEmpty() || $bukus->isEmpty()) {
            $this->command->warn('Jalankan UserSeeder & BukuSeeder dulu sebelum PeminjamanSeeder.');
            return;
        }

        // -----------------------------------------------------------
        // 1. Selesai TEPAT WAKTU (status: selesai, denda: 0)
        // -----------------------------------------------------------
        $this->buatPeminjaman(
            user: $anggota[0],
            buku: $bukus[0],
            tanggalPinjam: now()->subDays(10),
            jatuhTempo: now()->subDays(10)->addDays($lamaPeminjaman),
            tanggalKembali: now()->subDays(5), // dikembalikan sebelum jatuh tempo
            loanService: $loanService
        );

        // -----------------------------------------------------------
        // 2. Selesai TELAT (status: selesai, denda dihitung LoanService)
        // -----------------------------------------------------------
        $this->buatPeminjaman(
            user: $anggota[1],
            buku: $bukus[1],
            tanggalPinjam: now()->subDays(15),
            jatuhTempo: now()->subDays(15)->addDays($lamaPeminjaman),
            tanggalKembali: now()->subDays(1), // dikembalikan telat beberapa hari
            loanService: $loanService
        );

        // -----------------------------------------------------------
        // 3. Masih DIPINJAM, belum jatuh tempo (status: dipinjam)
        // -----------------------------------------------------------
        $this->buatPeminjaman(
            user: $anggota[2],
            buku: $bukus[2],
            tanggalPinjam: now()->subDays(2),
            jatuhTempo: now()->subDays(2)->addDays($lamaPeminjaman),
            tanggalKembali: null,
            loanService: $loanService
        );

        // -----------------------------------------------------------
        // 4. Sudah lewat jatuh tempo tapi BELUM ditandai terlambat
        //    (skenario buat testing `php artisan loans:check-due`)
        // -----------------------------------------------------------
        $this->buatPeminjaman(
            user: $anggota[3],
            buku: $bukus[3],
            tanggalPinjam: now()->subDays($lamaPeminjaman + 5),
            jatuhTempo: now()->subDays(5), // sudah lewat 5 hari
            tanggalKembali: null,
            loanService: $loanService
        );

        // -----------------------------------------------------------
        // 5. Jatuh tempo persis H-3 dari sekarang
        //    (skenario buat testing notifikasi reminder H-3)
        // -----------------------------------------------------------
        $hMinus = (int) config('library.notif_h_minus', 3);
        $this->buatPeminjaman(
            user: $anggota[4],
            buku: $bukus[4],
            tanggalPinjam: now()->subDays($lamaPeminjaman - $hMinus),
            jatuhTempo: now()->addDays($hMinus),
            tanggalKembali: null,
            loanService: $loanService
        );

        // -----------------------------------------------------------
        // 6. Sudah berstatus 'terlambat' (hasil scheduler kemarin),
        //    belum dikembalikan sama sekali
        // -----------------------------------------------------------
        $this->buatPeminjaman(
            user: $anggota[0],
            buku: $bukus[5],
            tanggalPinjam: now()->subDays($lamaPeminjaman + 10),
            jatuhTempo: now()->subDays(10),
            tanggalKembali: null,
            loanService: $loanService
        );

        $this->command->info('PeminjamanSeeder selesai: 6 skenario transaksi dibuat.');
    }

    /**
     * Helper: buat satu Peminjaman + PeminjamanDetail + hitung denda
     * pakai LoanService (bukan hardcode manual), supaya data seed
     * konsisten dengan logic aplikasi yang sebenarnya.
     */
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
            'user_id'        => $user->id,
            'tanggal_pinjam' => $tanggalPinjam,
            'jatuh_tempo'    => $jatuhTempo,
            'tanggal_kembali'=> $tanggalKembali,
            'status'         => 'dipinjam', // sementara, diupdate di bawah
            'denda'          => 0,
        ]);

        PeminjamanDetail::create([
            'peminjaman_id' => $peminjaman->id,
            'buku_id'       => $buku->id,
            'jumlah'        => 1,
        ]);

        $peminjaman->load('detail.buku');

        if ($statusManual) {
            // Skenario khusus: status dipaksa manual (belum diproses scheduler)
            $peminjaman->status = $statusManual;
        } elseif ($tanggalKembali) {
            // Sudah dikembalikan -> hitung denda pakai LoanService, status selesai
            $denda = $loanService->hitungDenda($peminjaman);
            $peminjaman->denda  = $denda;
            $peminjaman->status = 'selesai';
        } else {
            // Masih dipinjam, belum lewat jatuh tempo
            $peminjaman->status = 'dipinjam';
        }

        $peminjaman->save();

        // Kurangi stok buku sesuai peminjaman (kecuali sudah dikembalikan,
        // supaya stok akhir hasil seed tetap masuk akal)
        if (!$tanggalKembali) {
            $buku->decrement('stok');
        }

        return $peminjaman;
    }
}
