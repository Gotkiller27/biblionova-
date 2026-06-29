<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseApiController
{
    public function __construct(
        private AuthService $authService
    ) {}

    /**
     * Register a new user
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register($request->validated());
            
            return $this->sendResponse([
                'user' => $result['user'],
                'token' => $result['token'],
                'redirect_url' => $this->authService->getRedirectUrlByRole($result['user']),
            ], $result['message']);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Login user
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());
            
            return $this->sendResponse([
                'user' => $result['user'],
                'token' => $result['token'],
                'redirect_url' => $this->authService->getRedirectUrlByRole($result['user']),
            ], $result['message']);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Logout user
     */
    public function logout(): JsonResponse
    {
        try {
            $result = $this->authService->logout();
            
            return $this->sendResponse(null, $result['message']);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Get authenticated user
     */
    public function me(): JsonResponse
    {
        $user = $this->authService->me();
        
        if (!$user) {
            return $this->sendError('Non authentifié', null, 401);
        }

        return $this->sendResponse([
            'user' => $user,
            'redirect_url' => $this->authService->getRedirectUrlByRole($user),
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->changePassword(
                $request->validated('current_password'),
                $request->validated('password')
            );
            
            return $this->sendResponse(null, $result['message']);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Send password reset email
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->forgotPassword(
                $request->validated('email')
            );
            
            return $this->sendResponse(null, $result['message']);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Reset password with token
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->resetPassword($request->validated());
            
            return $this->sendResponse(null, $result['message']);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Check authentication status
     */
    public function checkAuth(): JsonResponse
    {
        $authenticated = Auth::check();
        
        return $this->sendResponse([
            'authenticated' => $authenticated,
            'user' => $authenticated ? Auth::user()->load('roles') : null,
        ]);
    }

    /**
     * Refresh user token
     */
    public function refresh(): JsonResponse
    {
        if (!Auth::check()) {
            return $this->sendError('Non authentifié', null, 401);
        }

        $user = Auth::user();
        
        // Delete current token
        $user->currentAccessToken()->delete();
        
        // Create new token
        $newToken = $user->createToken('auth-token')->plainTextToken;
        
        return $this->sendResponse([
            'user' => $user->load('roles'),
            'token' => $newToken,
            'redirect_url' => $this->authService->getRedirectUrlByRole($user),
        ], 'Token rafraîchi');
    }
}