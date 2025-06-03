<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class LimitRequestMiddelware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);
    // }

    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        $key = $request->ip(); // You can customize the key based on your needs

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'code' => 400,
                'message' => 'silahkan coba beberapa saat lagi yaa..',
            ], 400);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $response;
    }
}
