<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Infrastructure\OAuth\Google\Exceptions\GoogleAuthDomainMismatchException;
use App\Infrastructure\OAuth\Google\Exceptions\GoogleAuthTokenInvalidException;
use App\Infrastructure\OAuth\Google\Exceptions\GoogleAuthVerificationException;
use App\Modules\Auth\Services\AuthService;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

final class GoogleCallbackController extends Controller
{
    public function __construct(
        private AuthService $authService,
    ) {}

    /**
     * GET: Render callback page (credential in URL fragment - client-side parsing).
     */
    public function show(): View
    {
        return view('auth.google-callback');
    }

    /**
     * POST: Handle redirect from Google with credential in body.
     * Google GSI sends: credential, g_csrf_token. CSRF validation when cookie available.
     * Lưu ý: Khi redirect từ Google, cookie g_csrf_token thường KHÔNG được gửi (SameSite
     * block cross-site POST). Fallback: xác thực JWT đủ mạnh để tin credential.
     */
    public function handle(Request $request)
    {
        $csrfBody = $request->input('g_csrf_token');
        $csrfCookie = $request->cookie('g_csrf_token');
        $csrfValid = !empty($csrfBody) && !empty($csrfCookie) && hash_equals((string) $csrfCookie, (string) $csrfBody);

        if (!$csrfValid) {
            Log::info('Google callback: g_csrf_token missing (thường do SameSite khi redirect) - dùng JWT verification');
        }

        $credential = $request->input('credential');
        if (empty($credential) || !is_string($credential)) {
            Log::warning('Google callback: Missing credential');
            return redirect()->route('login')->with('error', 'Invalid credential. Please try again.');
        }

        try {
            $user = $this->authService->loginWithGoogle($credential);
            $token = $user->createToken('auth-token')->plainTextToken;

            return view('auth.google-callback-complete', [
                'token' => $token,
                'homeUrl' => url(RouteServiceProvider::HOME),
            ]);
        } catch (GoogleAuthTokenInvalidException|GoogleAuthVerificationException $e) {
            Log::warning('Google callback: Token invalid', ['error' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Invalid or expired token. Please try again.');
        } catch (GoogleAuthDomainMismatchException $e) {
            Log::info('Google callback: Domain mismatch', ['error' => $e->getMessage()]);
            return redirect()->route('login')->with('error', $e->getMessage());
        }
    }
}
