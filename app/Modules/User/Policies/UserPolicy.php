<?php

declare(strict_types=1);

namespace App\Modules\User\Policies;

use App\Models\User;

final class UserPolicy
{
    public function view(User $authenticatedUser, User $user): bool
    {
        return $authenticatedUser->id === $user->id || $authenticatedUser->hasRole('admin');
    }

    public function update(User $authenticatedUser, User $user): bool
    {
        return $authenticatedUser->id === $user->id;
    }
}
