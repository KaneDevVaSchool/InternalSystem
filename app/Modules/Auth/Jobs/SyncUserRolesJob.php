<?php

declare(strict_types=1);

namespace App\Modules\Auth\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class SyncUserRolesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private int $userId,
    ) {}

    public function handle(): void
    {
        $user = User::query()->find($this->userId);

        if ($user === null) {
            return;
        }

        $adminEmails = config('auth.admin_emails', []);

        if (in_array($user->email, $adminEmails, true)) {
            $user->syncRoles(['admin']);
        } else {
            $user->syncRoles(['user']);
        }
    }
}
