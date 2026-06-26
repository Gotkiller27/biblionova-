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
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->status === 'inactive') {
                Auth::logout();
                return ApiResponse::unauthorized('Votre compte est inactif');
            }
            
            if ($user->status === 'suspended') {
                Auth::logout();
                return ApiResponse::forbidden('Votre compte est suspendu');
            }
            
            if ($user->status === 'banned') {
                Auth::logout();
                return ApiResponse::forbidden('Votre compte est banni');
            }
        }

        return $next($request);
    }
}