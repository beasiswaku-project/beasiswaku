<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restrict a route to users having one of the given roles.
 *
 * Usage: `->middleware('role:admin')` or `->middleware('role:admin,mahasiswa')`.
 */
class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $allowedRoles = array_map(UserRole::from(...), $roles);

        abort_unless($request->user()?->hasRole(...$allowedRoles), Response::HTTP_FORBIDDEN);

        return $next($request);
    }
}
