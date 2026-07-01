<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;

class CheckAccountStatus
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if ($user) {
            if ($user->status === 'inactive') {
                if ($user->currentAccessToken()) {
                    $user->currentAccessToken()->delete();
                }
                Auth::guard('web')->logout();
                return ApiResponse::unauthorized('Votre compte est inactif');
            }
            
            if ($user->status === 'suspended') {
                if ($user->currentAccessToken()) {
                    $user->currentAccessToken()->delete();
                }
                Auth::guard('web')->logout();
                return ApiResponse::forbidden('Votre compte est suspendu');
            }
            
            if ($user->status === 'banned') {
                if ($user->currentAccessToken()) {
                    $user->currentAccessToken()->delete();
                }
                Auth::guard('web')->logout();
                return ApiResponse::forbidden('Votre compte est banni');
            }
        }

        return $next($request);
    }
}