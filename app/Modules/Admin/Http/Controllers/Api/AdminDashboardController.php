<?php

declare(strict_types=1);

namespace App\Modules\Admin\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Services\AdminDashboardService;
use Illuminate\Http\JsonResponse;

final class AdminDashboardController extends Controller
{
    public function __construct(
        private AdminDashboardService $dashboardService,
    ) {}

    /**
     * Get admin dashboard statistics.
     */
    public function stats(): JsonResponse
    {
        $stats = $this->dashboardService->getDashboardStats();

        return response()->json(['stats' => $stats->toArray()]);
    }
}
