<?php

namespace App\Notifications;

use App\Models\Peminjaman;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class PeminjamanBerhasilNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Peminjaman $peminjaman)
    {
    }

    /**
     * Kirim ke database (panel notif) dan email sekaligus.
     */
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

        $tanggalKembali = optional($this->peminjaman->jatuh_tempo)
            ->format('d M Y');

        return (new MailMessage)
            ->subject('Peminjaman Buku Berhasil - MOCO')
            ->greeting('Halo, ' . $notifiable->nama . '!')
            ->line('Peminjaman buku kamu berhasil diproses.')
            ->line('Judul buku: ' . ($judulBuku ?: '-'))
            ->line('Batas pengembalian: ' . ($tanggalKembali ?: '-'))
            ->line('Jangan lupa kembalikan tepat waktu supaya terhindar dari denda.')
            ->action('Lihat Peminjaman Saya', route('member.loans.index'))
            ->line('Terima kasih sudah menggunakan MOCO.');
    }

    /**
     * Disimpan ke tabel notifications, dipakai buat render panel notif
     * di layout (data['title'], data['message'], data['type'], dst).
     */
    public function toArray(object $notifiable): array
    {
        $judulBuku = $this->peminjaman->details
            ->pluck('buku.judul')
            ->filter()
            ->implode(', ');

        return [
            'type' => 'loan_request',
            'title' => 'Peminjaman Berhasil',
            'message' => 'Peminjaman "' . ($judulBuku ?: 'buku') . '" berhasil diproses.',
            'action_url' => route('member.loans.show', $this->peminjaman->id),
            'peminjaman_id' => $this->peminjaman->id,
        ];
    }
}
