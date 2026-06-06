<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('admin')->user();
        if ($user && ! blank($user->google2fa_secret) && !session('2fa_passed')) {
            return redirect()->route('admin.profile.confirmationCode');
        }
        return $next($request);
    }
}
