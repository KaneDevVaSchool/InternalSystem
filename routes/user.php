<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| User Routes (API)
|--------------------------------------------------------------------------
| Loaded via RouteServiceProvider. Prefix: /api/user
|
*/

use App\Modules\User\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'permission:user.profile.view'])->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
});
