<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CenterFileMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $allowedIps = [
                "103.153.60.67",
                "103.78.11.202",
                "192.168.10.29",
                "127.0.0.1",
                "0.0.0.0",
            ];

            Log::info('CENTER FILE MIDDLEWARE DEBUG', [
                'request_ip' => $request->ip(),
                'request_ips' => $request->ips(),
                'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? null,
                'x_forwarded_for' => $request->header('X-Forwarded-For'),
                'x_real_ip' => $request->header('X-Real-IP'),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
            ]);

            if (in_array($request->ip(), $allowedIps)) {
                return $next($request);
            }

            return response()->json([
                'success' => false,
                'message' => 'unauthorized',
                'debug_ip' => $request->ip(), // sementara aja
            ], 401);
        } catch (\Throwable $th) {
            Log::error('CENTER FILE MIDDLEWARE ERROR', [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'unauthorized'
            ], 401);
        }
    }
}
