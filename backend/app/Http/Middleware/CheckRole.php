<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|array  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return ApiResponse::unauthorized('Authentification requise');
        }

        $user = Auth::user();
        
        // Check if user has any of the required roles
        if (!$user->hasAnyRole($roles)) {
            return ApiResponse::forbidden('Accès refusé. Rôle requis : ' . implode(', ', $roles));
        }

        return $next($request);
    }
}