<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['layouts.app', 'layouts.admin'], function ($view) {
            if (auth()->check()) {
                $view->with(
                    'notifications',
                    // Cuma 3 terbaru buat dropdown panel di navbar.
                    // Daftar lengkap tetap ada di halaman "Lihat Semua"
                    // (member.notifications.index / admin.notifications.index)
                    // yang di-paginate 10 per halaman.
                    auth()->user()->notifications()->latest()->take(3)->get()
                );
                $view->with(
                    'unreadNotifCount',
                    auth()->user()->unreadNotifications()->count()
                );
            } else {
                $view->with('notifications', collect());
                $view->with('unreadNotifCount', 0);
            }
        });
    }
}
