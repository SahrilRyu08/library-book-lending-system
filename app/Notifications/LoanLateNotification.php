<?php

namespace App\Notifications;

use App\Models\Peminjaman;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LoanLateNotification extends Notification
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

            'title' => 'Peminjaman Terlambat',

            'message' => sprintf(
                'Peminjaman #%d telah melewati batas pengembalian.',
                $this->loan->id
            ),

            'loan_id' => $this->loan->id,

            'type' => 'late',

            'action_url' => route(
                'member.loans.show',
                $this->loan->id
            ),

        ];
    }
}
