<?php

declare(strict_types=1);

namespace App\Infrastructure\OAuth\Google;

use App\Infrastructure\OAuth\Google\DTO\GoogleUserPayload;
use App\Infrastructure\OAuth\Google\Exceptions\GoogleAuthDomainMismatchException;
use App\Infrastructure\OAuth\Google\Exceptions\GoogleAuthTokenInvalidException;
use App\Infrastructure\OAuth\Google\Exceptions\GoogleAuthVerificationException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final class GoogleAuthService
{
    private const CERTS_URL = 'https://www.googleapis.com/oauth2/v1/certs';
    private const CERTS_CACHE_KEY = 'google_oauth2_certs';
    private const CERTS_CACHE_TTL = 3600; // 1 hour

    /**
     * @param  array<int, string>  $allowedDomains
     */
    public function __construct(
        private string $clientId,
        private array $allowedDomains,
    ) {}

    /**
     * Verify Google ID token and extract user payload.
     * Uses firebase/php-jwt + Google's public certs (no Google\Client dependency).
     *
     * @throws GoogleAuthTokenInvalidException
     * @throws GoogleAuthDomainMismatchException
     * @throws GoogleAuthVerificationException
     */
    public function verifyIdToken(string $idToken): GoogleUserPayload
    {
        $kid = $this->getTokenKid($idToken);
        $publicKey = $this->getPublicKeyForKid($kid);

        try {
            $payload = JWT::decode($idToken, new Key($publicKey, 'RS256'));
        } catch (\Throwable $e) {
            throw new GoogleAuthTokenInvalidException('Invalid or expired ID token: ' . $e->getMessage());
        }

        $payload = (array) $payload;

        if (($payload['aud'] ?? '') !== $this->clientId && ($payload['azp'] ?? '') !== $this->clientId) {
            throw new GoogleAuthTokenInvalidException('Token audience mismatch.');
        }

        $issuers = ['https://accounts.google.com', 'accounts.google.com'];
        if (!in_array($payload['iss'] ?? '', $issuers, true)) {
            throw new GoogleAuthTokenInvalidException('Invalid token issuer.');
        }

        $emailVerified = $payload['email_verified'] ?? false;
        if ($emailVerified !== true && $emailVerified !== 'true') {
            throw new GoogleAuthVerificationException('Email not verified.');
        }

        $hd = $payload['hd'] ?? null;
        if ($hd === null || !in_array($hd, $this->allowedDomains, true)) {
            throw new GoogleAuthDomainMismatchException(
                sprintf(
                    "Domain '%s' is not allowed. Allowed: @%s",
                    (string) $hd,
                    implode(', @', $this->allowedDomains)
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

    private function getTokenKid(string $idToken): ?string
    {
        $parts = explode('.', $idToken);
        if (count($parts) < 2) {
            return null;
        }
        $decoded = base64_decode(strtr($parts[0], '-_', '+/'), true);
        $header = is_string($decoded) ? json_decode($decoded, true) : null;
        return is_array($header) ? ($header['kid'] ?? null) : null;
    }

    private function getPublicKeyForKid(?string $kid): string
    {
        $certs = $this->fetchCerts();

        $toTry = $kid !== null && isset($certs[$kid]) ? [$kid => $certs[$kid]] : $certs;

        foreach ($toTry as $pem) {
            $key = $this->extractPublicKeyFromPem($pem);
            if ($key !== null) {
                return $key;
            }
        }

        throw new GoogleAuthTokenInvalidException('Could not retrieve Google public key.');
    }

    private function extractPublicKeyFromPem(string $pem): ?string
    {
        if (str_contains($pem, '-----BEGIN CERTIFICATE-----')) {
            $cert = openssl_x509_read($pem);
            if ($cert !== false) {
                $key = openssl_pkey_get_public($cert);
                if ($key !== false) {
                    $details = openssl_pkey_get_details($key);
                    return $details['key'] ?? null;
                }
            }
        }
        return str_contains($pem, '-----BEGIN') ? $pem : null;
    }

    /**
     * @return array<string, string>
     */
    private function fetchCerts(): array
    {
        return Cache::remember(self::CERTS_CACHE_KEY, self::CERTS_CACHE_TTL, function () {
            $response = Http::timeout(10)->get(self::CERTS_URL);
            if (!$response->successful()) {
                throw new GoogleAuthTokenInvalidException('Failed to fetch Google certs.');
            }
            $data = $response->json();
            return is_array($data) ? $data : [];
        });
    }
}
