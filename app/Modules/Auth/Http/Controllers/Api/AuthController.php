<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Infrastructure\OAuth\Google\Exceptions\GoogleAuthDomainMismatchException;
use App\Infrastructure\OAuth\Google\Exceptions\GoogleAuthTokenInvalidException;
use App\Infrastructure\OAuth\Google\Exceptions\GoogleAuthVerificationException;
use App\Modules\Auth\Http\Requests\GoogleLoginRequest;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

final class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
    ) {}

    /**
     * Login with Google ID Token.
     * Frontend sends credential.credential from Google One Tap / Sign-In.
     */
    public function googleLogin(GoogleLoginRequest $request): JsonResponse
    {
        $idToken = $request->validated('id_token');
        if (config('app.debug')) {
            Log::debug('Google login request', [
                'token_length' => strlen($idToken),
                'token_preview' => substr($idToken, 0, 20) . '...',
            ]);
        }
        try {
            $user = $this->authService->loginWithGoogle($idToken);

            $token = $user->createToken('auth-token')->plainTextToken;
            if (config('app.debug')) {
                Log::debug('Google login success', ['user_id' => $user->id, 'email' => $user->email]);
            }

            return response()->json([
                'message' => 'Authenticated successfully.',
                'user' => $user->getSafeApiAttributes(),
                'token' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (GoogleAuthTokenInvalidException $e) {
            Log::warning('Google login failed: invalid token', ['error' => $e->getMessage()]);
            if (config('app.debug')) {
                Log::debug('Google login failure detail', ['exception' => get_class($e), 'trace' => $e->getTraceAsString()]);
            }
            return response()->json(['message' => 'Invalid or expired token.'], 401);
        } catch (GoogleAuthDomainMismatchException $e) {
            Log::info('Google login rejected: domain mismatch', ['error' => $e->getMessage()]);
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (GoogleAuthVerificationException $e) {
            Log::warning('Google login failed: verification', ['error' => $e->getMessage()]);
            if (config('app.debug')) {
                Log::debug('Google login failure detail', ['exception' => get_class($e)]);
            }
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    /**
     * Logout (revoke current token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->recordLogout();
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    /**
     * Get authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user()->getSafeApiAttributes(),
        ]);
    }
}
