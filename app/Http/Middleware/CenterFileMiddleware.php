<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use function Laravel\Prompts\error;

class CenterFileMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {

            if ($request->ip() == "103.153.60.67" || $request->ip() == "103.78.11.202" || $request->ip() == "127.0.0.1") {
                return $next($request);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'unautorized'
                ], 401);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'unautorized'
            ], 401);
        }
    }
}
