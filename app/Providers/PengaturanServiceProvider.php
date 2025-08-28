<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\PengaturanAplikasi;

class PengaturanServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share pengaturan aplikasi to all views
        View::composer('*', function ($view) {
            try {
                $pengaturan = PengaturanAplikasi::first();
                $view->with('pengaturan', $pengaturan);
            } catch (\Exception $e) {
                // Fallback jika ada error
                $view->with('pengaturan', null);
            }
        });
    }
}
