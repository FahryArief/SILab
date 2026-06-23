<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        Schema::defaultStringLength(191);

        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            if (auth()->check()) {
                $notifCount = 0;
                $userRole = auth()->user()->role;
                if ($userRole === 'teknisi' || $userRole === 'super_admin') {
                    $notifCount = \App\Models\Peminjaman::where('status', 'pending')->count()
                                + \App\Models\BookingRuangan::where('status', 'pending')->count();
                } elseif ($userRole === 'kepala_lab') {
                    $notifCount = \App\Models\Peminjaman::where('status', 'divalidasi_teknisi')->count();
                }
                $view->with('notifCount', $notifCount);
            }
        });
    }
}
