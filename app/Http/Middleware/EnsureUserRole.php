<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (! $request->user() || ! in_array($request->user()->role->value, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
