<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds headers required for Google Sign-In popup flow.
 * Cross-Origin-Opener-Policy: same-origin-allow-popups allows the Google
 * OAuth popup to communicate with the opener via postMessage.
 */
final class GoogleSignInHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');
        return $response;
    }
}
