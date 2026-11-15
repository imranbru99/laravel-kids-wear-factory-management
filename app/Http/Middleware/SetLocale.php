<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = (string) $request->route('locale');
        abort_unless(array_key_exists($locale, config('kidswear.locales', [])), 404);
        app()->setLocale($locale);
        session(['locale' => $locale]);

        return $next($request);
    }
}
