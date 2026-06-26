<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|array  ...$permissions
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        if (!Auth::check()) {
            return ApiResponse::unauthorized('Authentification requise');
        }

        $user = Auth::user();
        
        // Check if user has any of the required permissions
        if (!$user->hasAnyPermission($permissions)) {
            return ApiResponse::forbidden('Permission refusée. Permission requise : ' . implode(', ', $permissions));
        }

        return $next($request);
    }
}