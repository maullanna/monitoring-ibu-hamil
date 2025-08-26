<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\PengaturanAplikasi;

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
        // Share pengaturan aplikasi to all admin views
        View::composer('layouts.admin', function ($view) {
            $pengaturan = PengaturanAplikasi::first();
            $view->with('pengaturan', $pengaturan);
        });
        
        // Share pengaturan aplikasi to all user views
        View::composer('layouts.user', function ($view) {
            $pengaturan = PengaturanAplikasi::first();
            $view->with('pengaturan', $pengaturan);
        });
    }
}
