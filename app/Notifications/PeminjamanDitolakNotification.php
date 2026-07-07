<?php

namespace App\Notifications;

use App\Models\Peminjaman;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class PeminjamanDitolakNotification extends Notification implements ShouldQueue
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
        $judulBuku = $this->loan->detail->first()->buku->judul ?? 'buku';
        $jumlahBuku = $this->loan->detail->count();

        $pesan = $jumlahBuku > 1
            ? "Pengajuan peminjaman \"{$judulBuku}\" dan {$jumlahBuku} buku lainnya ditolak."
            : "Pengajuan peminjaman \"{$judulBuku}\" ditolak.";

        return [
            'title'   => 'Peminjaman Ditolak',
            'message' => $pesan,
            'loan_id' => $this->loan->id,
            'type'    => 'peminjaman_ditolak',
        ];
    }
}
