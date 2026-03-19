<?php

namespace App\Providers;

use App\Models\User;
use App\Models\UserInfo;
use App\Modules\User\Policies\UserInfoPolicy;
use App\Modules\User\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        UserInfo::class => UserInfoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(function (User $user, string $ability) {
            $perms = $user->getAllPermissions()->pluck('name')->toArray();
            if (in_array($ability, $perms, true)) {
                return true;
            }
        });
    }
}
