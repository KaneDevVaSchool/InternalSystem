<?php

declare(strict_types=1);

namespace App\Modules\Admin\DTO;

final readonly class DashboardStatsDTO
{
    public function __construct(
        public int $totalUsers,
        public int $adminsCount,
    ) {}

    /**
     * @return array<string, int>
     */
    public function toArray(): array
    {
        return [
            'total_users' => $this->totalUsers,
            'admins_count' => $this->adminsCount,
        ];
    }
}
