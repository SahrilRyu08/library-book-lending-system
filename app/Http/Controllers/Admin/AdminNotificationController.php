<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    /**
     * Daftar notifikasi admin (terbaru di urutan atas).
     */
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(10);

        // Dihitung terpisah dari $notifications karena itu sudah di-paginate
        // (cuma 10 item per halaman) — kalau dihitung dari situ, tombol
        // "Tandai Semua Dibaca" bisa hilang padahal masih ada unread
        // di halaman lain.
        $unreadCount = $request->user()->unreadNotifications()->count();

        return view(
            'admin.notifications.index',
            compact('notifications', 'unreadCount')
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
