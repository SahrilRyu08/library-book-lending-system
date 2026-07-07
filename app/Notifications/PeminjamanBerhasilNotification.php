<?php

namespace App\Notifications;

use App\Models\Peminjaman;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Dikirim saat anggota SELESAI mengajukan peminjaman (status masih 'menunggu',
 * belum dikonfirmasi admin). Untuk notifikasi saat admin sudah konfirmasi
 * (status jadi 'dipinjam'), pakai PeminjamanDikonfirmasiNotification.
 */
class PeminjamanBerhasilNotification extends Notification implements ShouldQueue
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
            ->subject('Pengajuan Peminjaman Diterima - MOCO')
            ->greeting('Halo, ' . $notifiable->nama . '!')
            ->line('Pengajuan peminjaman buku kamu sudah kami terima dan sedang menunggu konfirmasi admin.')
            ->line('Judul buku: ' . ($judulBuku ?: '-'))
            ->line('Estimasi batas pengembalian: ' . ($jatuhTempo ?: '-'))
            ->line('Kamu akan mendapat notifikasi begitu admin mengonfirmasi peminjaman ini.')
            ->action('Lihat Peminjaman Saya', route('member.loans.index'));
    }

    public function toArray(object $notifiable): array
    {
        $judulBuku = $this->peminjaman->detail
            ->pluck('buku.judul')
            ->filter()
            ->implode(', ');

        return [
            'type'          => 'loan_request',
            'title'         => 'Pengajuan Diterima',
            'message'       => 'Pengajuan peminjaman "' . ($judulBuku ?: 'buku') . '" sedang menunggu konfirmasi admin.',
            'action_url'    => route('member.loans.show', $this->peminjaman->id),
            'peminjaman_id' => $this->peminjaman->id,
        ];
    }
}
