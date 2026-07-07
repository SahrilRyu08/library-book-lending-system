<?php

namespace App\Notifications;

use App\Models\Peminjaman;
use App\Services\LoanService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class KeterlambatanNotification extends Notification implements ShouldQueue
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

        // Pakai LoanService biar rumus denda cuma ada di satu tempat,
        // konsisten dengan yang ditampilkan di view member/admin.
        $estimasiDenda = app(LoanService::class)->hitungDenda($this->peminjaman);

        return (new MailMessage)
            ->subject('Peminjaman Buku Terlambat - MOCO')
            ->greeting('Halo, ' . $notifiable->nama . '!')
            ->line('Peminjaman buku kamu sudah melewati batas pengembalian.')
            ->line('Judul buku: ' . ($judulBuku ?: '-'))
            ->line('Batas pengembalian: ' . ($jatuhTempo ?: '-'))
            ->line('Estimasi denda saat ini: Rp ' . number_format($estimasiDenda, 0, ',', '.'))
            ->line('Denda terus bertambah setiap hari sampai buku dikembalikan.')
            ->action('Lihat Peminjaman Saya', route('member.loans.index'))
            ->line('Segera kembalikan buku untuk menghindari denda lebih besar.');
    }

    public function toArray(object $notifiable): array
    {
        $judulBuku = $this->peminjaman->detail
            ->pluck('buku.judul')
            ->filter()
            ->implode(', ');

        return [
            'type'          => 'late',
            'title'         => 'Peminjaman Terlambat',
            'message'       => 'Buku "' . ($judulBuku ?: '-') . '" sudah melewati batas pengembalian.',
            'action_url'    => route('member.loans.show', $this->peminjaman->id, absolute: false),
            'peminjaman_id' => $this->peminjaman->id,
        ];
    }
}
