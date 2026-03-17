<?php

declare(strict_types=1);

namespace App\Infrastructure\OAuth\Google\DTO;

use Illuminate\Contracts\Support\Arrayable;

final class GoogleUserPayload implements Arrayable
{
    public function __construct(
        public readonly string $email,
        public readonly string $name,
        public readonly ?string $avatar = null,
        public readonly ?string $googleId = null,
    ) {}

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'name' => $this->name,
            'avatar' => $this->avatar,
            'google_id' => $this->googleId,
        ];
    }
}
