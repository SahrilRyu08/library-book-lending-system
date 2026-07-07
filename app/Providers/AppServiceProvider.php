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
        View::composer(['layouts.app', 'layouts.admin'], function ($view) {
            if (auth()->check()) {
                $view->with(
                    'notifications',
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
