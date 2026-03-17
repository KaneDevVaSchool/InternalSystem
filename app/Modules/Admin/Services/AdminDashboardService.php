<?php

declare(strict_types=1);

namespace App\Modules\Admin\Services;

use App\Modules\Admin\DTO\DashboardStatsDTO;
use App\Modules\Admin\Repositories\AdminStatsRepository;

final class AdminDashboardService
{
    public function __construct(
        private AdminStatsRepository $statsRepository,
    ) {}

    public function getDashboardStats(): DashboardStatsDTO
    {
        return new DashboardStatsDTO(
            totalUsers: $this->statsRepository->getTotalUsers(),
            adminsCount: $this->statsRepository->getAdminsCount(),
        );
    }
}
