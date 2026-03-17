<?php

declare(strict_types=1);

namespace App\Modules\Auth\Services;

use App\Infrastructure\OAuth\Google\GoogleAuthService;
use App\Infrastructure\OAuth\Google\DTO\GoogleUserPayload;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class AuthService
{
    private const ADMIN_EMAILS_CACHE_KEY = 'auth:admin_emails';
    private const ADMIN_EMAILS_CACHE_TTL = 3600; // 1 hour

    public function __construct(
        private GoogleAuthService $googleAuthService,
    ) {}

    /**
     * Login or register user via Google ID token.
     * Creates user if not exists, assigns role (admin if in whitelist, else user).
     */
    public function loginWithGoogle(string $idToken): User
    {
        $payload = $this->googleAuthService->verifyIdToken($idToken);

        return DB::transaction(function () use ($payload) {
            $user = User::query()->where('email', $payload->email)->first();

            if ($user === null) {
                $user = $this->createUser($payload);
            } else {
                $user = $this->updateUserProfile($user, $payload);
            }

            $this->syncUserRole($user);
            $user->recordLogin();

            return $user;
        });
    }

    private function createUser(GoogleUserPayload $payload): User
    {
        $user = User::query()->create([
            'name' => $payload->name,
            'email' => $payload->email,
            'avatar' => $payload->avatar,
            'google_id' => $payload->googleId,
            'email_verified_at' => now(),
            'password' => null,
        ]);

        $user->userInfo()->create([]);

        return $user;
    }

    private function updateUserProfile(User $user, GoogleUserPayload $payload): User
    {
        $user->update([
            'name' => $payload->name,
            'avatar' => $payload->avatar,
            'google_id' => $payload->googleId ?? $user->google_id,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ]);

        if (!$user->userInfo) {
            $user->userInfo()->create([]);
        }

        return $user;
    }

    private function syncUserRole(User $user): void
    {
        $adminEmails = $this->getAdminEmails();

        if (in_array($user->email, $adminEmails, true)) {
            $user->syncRoles(['admin']);
        } else {
            $user->syncRoles(['user']);
        }
    }

    /**
     * Get admin whitelist emails from config (cached).
     *
     * @return array<int, string>
     */
    private function getAdminEmails(): array
    {
        return Cache::driver('file')->remember(
            self::ADMIN_EMAILS_CACHE_KEY,
            self::ADMIN_EMAILS_CACHE_TTL,
            fn () => config('auth.admin_emails', [])
        );
    }
}
