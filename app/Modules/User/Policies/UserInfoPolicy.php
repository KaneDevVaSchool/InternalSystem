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
        if ($authenticatedUser->id === $userInfo->user_id) {
            return $authenticatedUser->can('user.info.view_sensitive');
        }
        return $authenticatedUser->can('admin.users.view');
    }

    public function update(User $authenticatedUser, UserInfo $userInfo): bool
    {
        if ($authenticatedUser->id === $userInfo->user_id) {
            return $authenticatedUser->can('user.profile.update');
        }
        return $authenticatedUser->can('admin.users.manage');
    }
}
