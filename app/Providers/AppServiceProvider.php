<?php

namespace App\Providers;

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
        foreach (['debug', 'info', 'error'] as $dir) {
            $path = storage_path("logs/{$dir}");
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
    }
}
