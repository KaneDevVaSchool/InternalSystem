<?php

declare(strict_types=1);

namespace App\Providers;

use App\Infrastructure\OAuth\Google\GoogleAuthService;
use Illuminate\Support\ServiceProvider;

final class GoogleAuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GoogleAuthService::class, function () {
            return new GoogleAuthService(
                clientId: (string) config('auth.google.client_id'),
                allowedDomain: (string) config('auth.google.allowed_domain'),
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
