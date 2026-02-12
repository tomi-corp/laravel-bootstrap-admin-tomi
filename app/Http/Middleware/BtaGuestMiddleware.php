<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BtaGuestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (auth('bta')->check()) {
                return redirect()->route('bta.dashboard.index');
            } else {
                return $next($request);
            }
        } catch (Exception $e) {
            return redirect()->route('bta.login');
        }
    }
}
