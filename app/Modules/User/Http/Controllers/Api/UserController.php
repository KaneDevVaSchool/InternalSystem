<?php

declare(strict_types=1);

namespace App\Modules\User\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\User\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class UserController extends Controller
{
    public function __construct(
        private UserService $userService,
    ) {}

    /**
     * Get current user profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $dto = $this->userService->getProfile($request->user());

        return response()->json(['profile' => $dto->toArray()]);
    }
}
