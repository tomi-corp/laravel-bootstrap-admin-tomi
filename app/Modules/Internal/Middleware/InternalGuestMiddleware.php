<?php

namespace App\Modules\Internal\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InternalGuestMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('admin')->check()) {
            return redirect()->route('internal.companies.index');
        }

        return $next($request);
    }
}
