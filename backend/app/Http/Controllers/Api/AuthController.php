<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @group Authentification
 *
 * API pour la gestion de l'authentification
 */
class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    /**
     * Connexion utilisateur
     *
     * Permet à un utilisateur de se connecter avec son email et mot de passe
     *
     * @unauthenticated
     * @bodyParam email string required Email de l'utilisateur. Example: admin@example.com
     * @bodyParam password string required Mot de passe de l'utilisateur. Example: password
     *
     * @response 200 {
     *  "success": true,
     *  "message": "Login successful",
     *  "data": {
     *    "user": { "id": 1, "first_name": "Admin", "last_name": "User", "email": "admin@example.com", "role": "admin" },
     *    "token": "1|abcdefghijklmnopqrstuvwxyz"
     *  }
     * }
     * @response 422 {
     *  "success": false,
     *  "message": "Erreur de validation",
     *  "errors": { "email": ["The provided credentials are incorrect."] }
     * }
     */
    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated());
        return ApiResponse::success([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], 'Login successful');
    }

    /**
     * Déconnexion utilisateur
     *
     * Révoque le token d'accès actuel
     *
     * @authenticated
     * @response 200 {
     *  "success": true,
     *  "message": "Logout successful",
     *  "data": null
     * }
     */
    public function logout()
    {
        $this->authService->logout(Auth::user());
        return ApiResponse::success(null, 'Logout successful');
    }

    /**
     * Utilisateur connecté
     *
     * Récupère les informations de l'utilisateur actuellement connecté
     *
     * @authenticated
     * @response 200 {
     *  "success": true,
     *  "message": "User retrieved successfully",
     *  "data": {
     *    "id": 1,
     *    "first_name": "Admin",
     *    "last_name": "User",
     *    "email": "admin@example.com",
     *    "role": "admin"
     *  }
     * }
     */
    public function me()
    {
        return ApiResponse::success(new UserResource(Auth::user()), 'User retrieved successfully');
    }

    /**
     * Rafraîchir le token
     *
     * Révoque le token actuel et génère un nouveau
     *
     * @authenticated
     * @response 200 {
     *  "success": true,
     *  "message": "Token refreshed",
     *  "data": {
     *    "user": { ... },
     *    "token": "2|newtoken123456"
     *  }
     * }
     */
    public function refresh()
    {
        $user = Auth::user();
        $user->tokens()->delete();
        $token = $user->createToken('auth-token')->plainTextToken;
        return ApiResponse::success([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Token refreshed');
    }

    /**
     * Changer le mot de passe
     *
     * Modifie le mot de passe de l'utilisateur connecté
     *
     * @authenticated
     * @bodyParam current_password string required Mot de passe actuel. Example: password
     * @bodyParam password string required Nouveau mot de passe (min 8 caractères). Example: newpassword123
     * @bodyParam password_confirmation string required Confirmation du nouveau mot de passe. Example: newpassword123
     *
     * @response 200 {
     *  "success": true,
     *  "message": "Password changed successfully",
     *  "data": null
     * }
     * @response 422 {
     *  "success": false,
     *  "message": "Erreur de validation",
     *  "errors": { "current_password": ["The current password is incorrect."] }
     * }
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $this->authService->changePassword(Auth::user(), $request->current_password, $request->password);
        return ApiResponse::success(null, 'Password changed successfully');
    }

    /**
     * Mot de passe oublié
     *
     * Envoie un email avec un lien de réinitialisation de mot de passe
     *
     * @unauthenticated
     * @bodyParam email string required Email de l'utilisateur. Example: user@example.com
     *
     * @response 200 {
     *  "success": true,
     *  "message": "Password reset link sent",
     *  "data": null
     * }
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $this->authService->forgotPassword($request->email);
        return ApiResponse::success(null, 'Password reset link sent');
    }

    /**
     * Réinitialiser le mot de passe
     *
     * Permet de réinitialiser le mot de passe avec le token reçu par email
     *
     * @unauthenticated
     * @bodyParam email string required Email de l'utilisateur. Example: user@example.com
     * @bodyParam token string required Token de réinitialisation. Example: abc123def456
     * @bodyParam password string required Nouveau mot de passe (min 8 caractères). Example: newpassword123
     * @bodyParam password_confirmation string required Confirmation. Example: newpassword123
     *
     * @response 200 {
     *  "success": true,
     *  "message": "Password reset successfully",
     *  "data": null
     * }
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->authService->resetPassword($request->validated());
        return ApiResponse::success(null, 'Password reset successfully');
    }
}
