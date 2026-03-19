<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsurePermission
{
    /**
     * Kiểm tra user có ít nhất một permission trong danh sách.
     *
     * @param  string  ...$permissions  Permission names, pipe-separated e.g. "admin.stats.view|admin.dashboard.view"
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $allowed = collect($permissions)->flatMap(fn (string $p) => explode('|', $p))->map('trim')->filter()->all();

        if (empty($allowed)) {
            return response()->json(['message' => 'Forbidden. No permission required.'], 403);
        }

        foreach ($allowed as $permission) {
            if ($request->user()->can($permission)) {
                return $next($request);
            }
        }

        return response()->json(['message' => 'Forbidden. Insufficient permissions.'], 403);
    }
}
