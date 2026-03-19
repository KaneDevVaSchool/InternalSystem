<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin Routes (API)
|--------------------------------------------------------------------------
| Loaded via RouteServiceProvider. Prefix: /api/admin
|
*/

use App\Modules\Admin\Http\Controllers\Api\AdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'permission:admin.stats.view'])->group(function () {
    Route::get('/stats', [AdminDashboardController::class, 'stats'])->name('admin.stats');
});
