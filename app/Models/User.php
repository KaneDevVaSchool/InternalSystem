<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'point',
        'contribution_point',
        'level',
        'google_id',
    ];

    /**
     * Những trường nhạy cảm - KHÔNG expose qua API.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google_access_token',
        'google_refresh_token',
        'google_token_expires_at',
        'google_scopes',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'point' => 'integer',
        'contribution_point' => 'integer',
        'check_first_login' => 'boolean',
        'first_login_at' => 'datetime',
        'last_login_at' => 'datetime',
        'last_logout_at' => 'datetime',
        'google_token_expires_at' => 'datetime',
        'google_scopes' => 'array',
        'strava_reconnect_suggested' => 'boolean',
    ];

    public function userInfo(): HasOne
    {
        return $this->hasOne(UserInfo::class);
    }

    /**
     * Dữ liệu an toàn cho API login - không expose token, password.
     */
    public function getSafeApiAttributes(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'point' => (int) ($this->point ?? 0),
            'contribution_point' => (int) ($this->contribution_point ?? 0),
            'level' => $this->level,
            'roles' => $this->getRoleNames()->toArray(),
        ];
    }

    /**
     * Cập nhật thời gian đăng nhập.
     */
    public function recordLogin(): void
    {
        $this->update([
            'last_login_at' => now(),
            'check_first_login' => $this->check_first_login ?: true,
            'first_login_at' => $this->first_login_at ?? now(),
        ]);
    }

    /**
     * Cập nhật thời gian đăng xuất.
     */
    public function recordLogout(): void
    {
        $this->update(['last_logout_at' => now()]);
    }
}
