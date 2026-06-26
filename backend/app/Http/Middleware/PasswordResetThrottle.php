<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use App\Helpers\ApiResponse;

class PasswordResetThrottle
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $key = 'password-reset:' . $request->ip();
        
        // Limite : 5 tentatives par heure
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            
            return ApiResponse::error(
                'Trop de tentatives. Réessayez dans ' . ceil($seconds / 60) . ' minutes.',
                null,
                429
            );
        }

        RateLimiter::increment($key, 3600); // 1 heure
        
        return $next($request);
    }
}