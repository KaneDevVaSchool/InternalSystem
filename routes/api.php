<?php

declare(strict_types=1);

use App\Modules\Auth\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Auth
|--------------------------------------------------------------------------
*/

// Public: Google OAuth login (rate limited)
Route::middleware('throttle:login')->group(function () {
    Route::post('/auth/google', [AuthController::class, 'googleLogin'])
        ->name('auth.google.login');
});

// Protected: Auth (logout, me)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');
});
