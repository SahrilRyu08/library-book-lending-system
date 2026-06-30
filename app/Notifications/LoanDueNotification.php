<?php

namespace App\Notifications;

use App\Models\Peminjaman;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LoanDueNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Peminjaman $loan
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Pengingat Pengembalian Buku',

            'message' => sprintf(
                'Peminjaman #%d akan jatuh tempo pada %s.',
                $this->loan->id,
                $this->loan->jatuh_tempo->format('d M Y')
            ),

            'loan_id' => $this->loan->id,

            'type' => 'due',

            'action_url' => route(
                'member.loans.show',
                $this->loan->id
            ),
        ];
    }
}
