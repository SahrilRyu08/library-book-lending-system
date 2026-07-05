<?php

namespace App\Notifications;

use App\Models\Peminjaman;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class JatuhTempoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Peminjaman $peminjaman,
        public int $hMinus = 3
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $judulBuku = $this->peminjaman->detail
            ->pluck('buku.judul')
            ->filter()
            ->implode(', ');

        $jatuhTempo = optional($this->peminjaman->jatuh_tempo)->format('d M Y');

        return (new MailMessage)
            ->subject("Pengingat: Batas Pengembalian {$this->hMinus} Hari Lagi - MOCO")
            ->greeting('Halo, ' . $notifiable->nama . '!')
            ->line("Buku yang kamu pinjam akan jatuh tempo dalam {$this->hMinus} hari.")
            ->line('Judul buku: ' . ($judulBuku ?: '-'))
            ->line('Batas pengembalian: ' . ($jatuhTempo ?: '-'))
            ->line('Segera kembalikan sebelum tanggal tersebut untuk menghindari denda.')
            ->action('Lihat Peminjaman Saya', route('member.loans.index'));
    }

    public function toArray(object $notifiable): array
    {
        $judulBuku = $this->peminjaman->detail
            ->pluck('buku.judul')
            ->filter()
            ->implode(', ');

        return [
            'type'          => 'due',
            'title'         => 'Segera Jatuh Tempo',
            'message'       => 'Buku "' . ($judulBuku ?: '-') . "\" jatuh tempo dalam {$this->hMinus} hari.",
            'action_url'    => route('member.loans.show', $this->peminjaman->id),
            'peminjaman_id' => $this->peminjaman->id,
        ];
    }
}
