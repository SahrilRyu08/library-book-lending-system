<?php

namespace App\Notifications;

use App\Models\Peminjaman;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReturnCompletedNotification extends Notification
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

            'title' => 'Pengembalian Berhasil',

            'message' => sprintf(
                'Peminjaman #%d telah berhasil dikembalikan.',
                $this->loan->id
            ),

            'loan_id' => $this->loan->id,

            'denda' => $this->loan->denda,

            'type' => 'returned',

            'action_url' => route(
                'member.loans.show',
                $this->loan->id
            ),

        ];
    }
}
