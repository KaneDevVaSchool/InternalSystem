<?php

declare(strict_types=1);

namespace App\Modules\User\DTO;

use App\Models\User;

final class ProfileDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?string $avatar,
        public int $point,
        public int $contributionPoint,
        public ?string $level,
        public array $roles,
        public array $permissions,
        public ?array $userInfoSafe,
        public ?array $userInfoSensitive,
    ) {}

    public static function fromUser(User $user, bool $includeSensitive = false): self
    {
        $user->load('userInfo');
        $info = $user->userInfo;

        $safe = $info ? UserInfoDTO::safe($info) : null;
        $sensitive = ($includeSensitive && $info) ? UserInfoDTO::sensitive($info) : null;

        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            avatar: $user->avatar,
            point: $user->point ?? 0,
            contributionPoint: $user->contribution_point ?? 0,
            level: $user->level,
            roles: $user->getRoleNames()->toArray(),
            permissions: $user->getAllPermissions()->pluck('name')->toArray(),
            userInfoSafe: $safe,
            userInfoSensitive: $sensitive,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $userInfo = $this->userInfoSafe ?? [];
        if ($this->userInfoSensitive !== null) {
            $userInfo = array_merge($userInfo, $this->userInfoSensitive);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'point' => $this->point,
            'contribution_point' => $this->contributionPoint,
            'level' => $this->level,
            'roles' => $this->roles,
            'permissions' => $this->permissions,
            'user_info' => !empty($userInfo) ? $userInfo : null,
        ];
    }
}
