<?php

declare(strict_types=1);

namespace App\Modules\User\Services;

use App\Models\User;
use App\Modules\User\DTO\ProfileDTO;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Gate;

final class UserService
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    /**
     * Lấy profile user. Dữ liệu nhạy cảm chỉ trả về khi có quyền (tự xem hoặc admin).
     */
    public function getProfile(User $user): ProfileDTO
    {
        $includeSensitive = false;
        if ($user->userInfo) {
            $includeSensitive = Gate::allows('viewSensitive', $user->userInfo);
        }

        return ProfileDTO::fromUser($user, $includeSensitive);
    }
}
