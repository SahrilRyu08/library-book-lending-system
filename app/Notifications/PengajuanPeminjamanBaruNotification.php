<?php

namespace App\Notifications;

use App\Models\Peminjaman;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class PengajuanPeminjamanBaruNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Peminjaman $loan)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $namaUser   = $this->loan->user->nama ?? 'Anggota';
        $judulBuku  = $this->loan->detail->first()->buku->judul ?? 'buku';
        $jumlahBuku = $this->loan->detail->count();

        $pesan = $jumlahBuku > 1
            ? "{$namaUser} mengajukan peminjaman \"{$judulBuku}\" dan " . ($jumlahBuku - 1) . " buku lainnya."
            : "{$namaUser} mengajukan peminjaman \"{$judulBuku}\".";

        return [
            'title'   => 'Pengajuan Peminjaman Baru',
            'message' => $pesan,
            'loan_id' => $this->loan->id,
            'type'    => 'pengajuan_baru',
        ];
    }
}
