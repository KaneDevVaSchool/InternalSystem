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
            $domains = config('auth.google.allowed_domains', []);
            if (empty($domains)) {
                $domains = ['your-domain.com'];
            }

            return new GoogleAuthService(
                clientId: (string) config('auth.google.client_id'),
                allowedDomains: $domains,
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
