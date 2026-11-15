<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $allowedRoles = array_map(fn (string $role) => UserRole::from($role), $roles);

        if (! $request->user()?->hasRole(...$allowedRoles)) {
            abort(403, 'You do not have permission to do this.');
        }

        return $next($request);
    }
}
