<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin/assets') || $request->is('admin/assets/*')) {
            return $next($request);
        }

        if (! $request->user()) {
            return redirect()->guest(route('login'));
        }

        if (! $request->user()->is_active) {
            auth()->logout();
            abort(403, 'This account is inactive.');
        }

        return $next($request);
    }
}
