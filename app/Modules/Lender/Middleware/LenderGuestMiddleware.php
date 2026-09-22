<?php

namespace App\Modules\Lender\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LenderGuestMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('lender')->check() && filled(auth('lender')->user()?->id_lender)) {
            return redirect()->route('lender.dashboard');
        }

        return $next($request);
    }
}
