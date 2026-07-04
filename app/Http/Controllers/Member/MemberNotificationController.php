<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MemberNotificationController extends Controller
{
    /**
     * Daftar notifikasi anggota (terbaru di urutan atas).
     */
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(10);

        return view(
            'member.notifications.index',
            compact('notifications')
        );
    }

    /**
     * Tandai satu notifikasi sebagai dibaca.
     */
    public function markRead(Request $request, string $id)
    {
        $notification = $request->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        return back()->with(
            'success',
            'Notifikasi berhasil dibaca.'
        );
    }

    /**
     * Tandai semua notifikasi sebagai dibaca.
     */
    public function markAllRead(Request $request)
    {
        $request->user()
            ->unreadNotifications
            ->markAsRead();

        return back()->with(
            'success',
            'Semua notifikasi berhasil dibaca.'
        );
    }
}
