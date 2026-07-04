<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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
        Paginator::useBootstrapFive();
        View::composer('layouts.app', function ($view) {

            if (!Auth::check()) {

                $view->with([
                    'notifications' => collect(),
                    'unreadNotifCount' => 0,
                ]);

                return;
            }

            $user = Auth::user();

            $view->with([
                'notifications' => $user->notifications()
                    ->latest()
                    ->take(5)
                    ->get(),

                'unreadNotifCount' => $user
                    ->unreadNotifications()
                    ->count(),
            ]);
        });
    }
}
