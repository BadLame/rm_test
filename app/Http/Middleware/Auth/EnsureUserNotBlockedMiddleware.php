<?php

namespace App\Http\Middleware\Auth;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class EnsureUserNotBlockedMiddleware
{
    function handle(Request $request, Closure $next): Response
    {
        /** @var User $user */
        $user = $request->user();

        if (!$user || $user->blocked_at) {
            throw new UnauthorizedHttpException('blocked');
        }

        return $next($request);
    }
}
