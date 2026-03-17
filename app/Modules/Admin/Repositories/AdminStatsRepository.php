<?php

declare(strict_types=1);

namespace App\Modules\Admin\Repositories;

use App\Models\User;

final class AdminStatsRepository
{
    public function getTotalUsers(): int
    {
        return User::query()->count();
    }

    public function getAdminsCount(): int
    {
        return User::query()
            ->whereHas('roles', fn ($q) => $q->where('name', 'admin'))
            ->count();
    }
}
