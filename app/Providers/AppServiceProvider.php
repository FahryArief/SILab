<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\BookingRuangan;
use App\Models\Peminjaman;
use App\Models\AuditPeriode;
use App\Support\Role;

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
                $userRole = auth()->user()->role;
                static $counts = [];
                if (! array_key_exists($userRole, $counts)) {
                    $counts[$userRole] = match ($userRole) {
                        Role::TEKNISI, Role::SUPER_ADMIN => Peminjaman::where('status', 'pending')->count()
                            + BookingRuangan::where('status', 'pending')->count()
                            + AuditPeriode::whereIn('status', ['open', 'revisi'])->count(),
                        Role::KEPALA_LAB => Peminjaman::where('status', 'divalidasi_teknisi')->count(),
                        default => 0,
                    };
                }
                $notifCount = $counts[$userRole];
                $view->with('notifCount', $notifCount);
            }
        });
    }
}
