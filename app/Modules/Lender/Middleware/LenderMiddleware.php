<?php

namespace App\Modules\Lender\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LenderMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('lender')->user();

        if (! auth('lender')->check() || blank($user?->id_lender)) {
            return redirect()->route('lender.login');
        }

        return $next($request);
    }
}
