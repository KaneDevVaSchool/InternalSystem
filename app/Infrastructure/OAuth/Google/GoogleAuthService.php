<?php

declare(strict_types=1);

namespace App\Infrastructure\OAuth\Google;

use App\Infrastructure\OAuth\Google\DTO\GoogleUserPayload;
use App\Infrastructure\OAuth\Google\Exceptions\GoogleAuthDomainMismatchException;
use App\Infrastructure\OAuth\Google\Exceptions\GoogleAuthTokenInvalidException;
use App\Infrastructure\OAuth\Google\Exceptions\GoogleAuthVerificationException;
use Google\Client as GoogleClient;

final class GoogleAuthService
{
    public function __construct(
        private string $clientId,
        private string $allowedDomain,
    ) {}

    /**
     * Verify Google ID token and extract user payload.
     * Rejects token if domain (hd claim) does not match allowed domain.
     *
     * @throws GoogleAuthTokenInvalidException
     * @throws GoogleAuthDomainMismatchException
     * @throws GoogleAuthVerificationException
     */
    public function verifyIdToken(string $idToken): GoogleUserPayload
    {
        $client = new GoogleClient(['client_id' => $this->clientId]);
        $payload = $client->verifyIdToken($idToken);

        if ($payload === false) {
            throw new GoogleAuthTokenInvalidException('Invalid or expired ID token.');
        }

        $payload = (array) $payload;

        // Verify email is verified
        $emailVerified = $payload['email_verified'] ?? false;
        if ($emailVerified !== true && $emailVerified !== 'true') {
            throw new GoogleAuthVerificationException('Email not verified.');
        }

        // CRITICAL: Validate hosted domain (hd claim)
        $hd = $payload['hd'] ?? null;
        if ($hd === null || $hd !== $this->allowedDomain) {
            throw new GoogleAuthDomainMismatchException(
                sprintf(
                    "Domain '%s' is not allowed. Only @%s accounts may sign in.",
                    (string) $hd,
                    $this->allowedDomain
                )
            );
        }

        return new GoogleUserPayload(
            email: (string) ($payload['email'] ?? ''),
            name: (string) ($payload['name'] ?? $payload['email'] ?? 'Unknown'),
            avatar: isset($payload['picture']) ? (string) $payload['picture'] : null,
            googleId: isset($payload['sub']) ? (string) $payload['sub'] : null,
        );
    }
}
