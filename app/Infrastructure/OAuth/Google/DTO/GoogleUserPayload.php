<?php

declare(strict_types=1);

namespace App\Infrastructure\OAuth\Google\DTO;

use Illuminate\Contracts\Support\Arrayable;

final readonly class GoogleUserPayload implements Arrayable
{
    public function __construct(
        public string $email,
        public string $name,
        public ?string $avatar = null,
        public ?string $googleId = null,
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
