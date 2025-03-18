<?php

namespace App\Http\Middleware\Auth;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserNotBlockedMiddleware
{
    function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = $request->user();

        if (!$user || ($user->blocked_at && !$user->is_admin)) {
            return response()->noContent(401);
        }

        return $next($request);
    }
}
