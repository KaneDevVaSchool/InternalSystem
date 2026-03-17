<?php

declare(strict_types=1);

namespace App\Modules\User\Policies;

use App\Models\User;
use App\Models\UserInfo;

final class UserInfoPolicy
{
    /**
     * Chỉ user xem chính mình hoặc admin mới xem được thông tin nhạy cảm (CCCD, ngân hàng, địa chỉ...).
     */
    public function viewSensitive(User $authenticatedUser, UserInfo $userInfo): bool
    {
        return $authenticatedUser->id === $userInfo->user_id
            || $authenticatedUser->hasRole('admin');
    }

    /**
     * Chỉ user tự sửa hoặc admin.
     */
    public function update(User $authenticatedUser, UserInfo $userInfo): bool
    {
        return $authenticatedUser->id === $userInfo->user_id
            || $authenticatedUser->hasRole('admin');
    }
}
