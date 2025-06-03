<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifEmailMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('api')->user()->email_verified_at !== null) {
            return $next($request);
        } else {
            return response()->json([
                'code' => 400,
                'message' => 'harap verifikasi nomor anda terlebih dahulu'
            ], 400);
        }
    }
}
