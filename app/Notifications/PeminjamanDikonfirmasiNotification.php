<?php

namespace App\Notifications;

use App\Models\Peminjaman;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Dikirim saat admin mengonfirmasi pengajuan (status: menunggu -> dipinjam).
 * Pasangan dari PeminjamanBerhasilNotification yang dikirim di tahap pengajuan.
 */
class PeminjamanDikonfirmasiNotification extends Notification implements ShouldQueue
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
        $judulBuku = $this->peminjaman->detail
            ->pluck('buku.judul')
            ->filter()
            ->implode(', ');

        $jatuhTempo = optional($this->peminjaman->jatuh_tempo)->format('d M Y');

        return (new MailMessage)
            ->subject('Peminjaman Dikonfirmasi - MOCO')
            ->greeting('Halo, ' . $notifiable->nama . '!')
            ->line('Peminjaman buku kamu sudah dikonfirmasi oleh admin dan resmi dipinjam.')
            ->line('Judul buku: ' . ($judulBuku ?: '-'))
            ->line('Batas pengembalian: ' . ($jatuhTempo ?: '-'))
            ->line('Jangan lupa kembalikan tepat waktu supaya terhindar dari denda.')
            ->action('Lihat Peminjaman Saya', route('member.loans.index'));
    }

    public function toArray(object $notifiable): array
    {
        $judulBuku = $this->peminjaman->detail
            ->pluck('buku.judul')
            ->filter()
            ->implode(', ');

        return [
            'type'          => 'loan_confirmed',
            'title'         => 'Peminjaman Dikonfirmasi',
            'message'       => 'Peminjaman "' . ($judulBuku ?: 'buku') . '" sudah dikonfirmasi admin.',
            'action_url'    => route('member.loans.show', $this->peminjaman->id),
            'peminjaman_id' => $this->peminjaman->id,
        ];
    }
}
