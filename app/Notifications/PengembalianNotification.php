<?php

namespace App\Notifications;

use App\Models\Peminjaman;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class PengembalianNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Peminjaman $peminjaman)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $judulBuku = $this->peminjaman->details
            ->pluck('buku.judul')
            ->filter()
            ->implode(', ');

        $mail = (new MailMessage)
            ->subject('Pengembalian Buku Berhasil - MOCO')
            ->greeting('Halo, ' . $notifiable->nama . '!')
            ->line('Pengembalian buku kamu sudah berhasil diproses.')
            ->line('Judul buku: ' . ($judulBuku ?: '-'));

        if ($this->peminjaman->denda > 0) {
            $mail->line('Total denda keterlambatan: Rp' . number_format($this->peminjaman->denda, 0, ',', '.'));
        } else {
            $mail->line('Terima kasih sudah mengembalikan tepat waktu!');
        }

        return $mail->action('Lihat Riwayat Peminjaman', route('member.loans.history'));
    }

    public function toArray(object $notifiable): array
    {
        $judulBuku = $this->peminjaman->details
            ->pluck('buku.judul')
            ->filter()
            ->implode(', ');

        return [
            'type'       => 'returned',
            'title'      => 'Pengembalian Berhasil',
            'message'    => 'Buku "' . ($judulBuku ?: '-') . '" berhasil dikembalikan.',
            'action_url' => route('member.loans.history'),
            'peminjaman_id' => $this->peminjaman->id,
        ];
    }
}
