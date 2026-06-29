<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new user
     */
    public function register(array $data): array
    {
        // Create user
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'status' => 'active',
            'email_verified_at' => null,
        ]);

        // Assign default role
        $user->assignRole('utilisateur');

        // Create Sanctum token
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'success' => true,
            'message' => 'Inscription réussie',
            'user' => $user->load('roles'),
            'token' => $token,
        ];
    }

    /**
     * Login user with Sanctum token authentication
     */
    public function login(array $credentials): array
    {
        // Extract remember me option
        $remember = $credentials['remember'] ?? false;
        unset($credentials['remember']);

        // Attempt login with web guard (for password verification)
        if (!Auth::guard('web')->attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Les informations d\'identification sont incorrectes.',
            ]);
        }

        $user = Auth::guard('web')->user();

        // Check if account is active
        if ($user->status !== 'active') {
            Auth::guard('web')->logout();
            throw ValidationException::withMessages([
                'email' => 'Votre compte est ' . $user->status . '.',
            ]);
        }

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Delete existing tokens
        $user->tokens()->delete();

        // Create new Sanctum token
        $token = $user->createToken('auth-token')->plainTextToken;

        // Logout from web guard (we only want token auth)
        Auth::guard('web')->logout();

        return [
            'success' => true,
            'message' => 'Connexion réussie',
            'user' => $user->load('roles'),
            'token' => $token,
        ];
    }

    /**
     * Logout user
     */
    public function logout(): array
    {
        $user = Auth::user();
        
        if ($user) {
            // Delete current token
            $user->currentAccessToken()->delete();
        }

        return [
            'success' => true,
            'message' => 'Déconnexion réussie',
        ];
    }

    /**
     * Get authenticated user
     */
    public function me(): ?User
    {
        return Auth::user()?->load('roles');
    }

    /**
     * Change user password
     */
    public function changePassword(string $currentPassword, string $newPassword): array
    {
        $user = Auth::user();

        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Le mot de passe actuel est incorrect.',
            ]);
        }

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return [
            'success' => true,
            'message' => 'Mot de passe modifié avec succès',
        ];
    }

    /**
     * Send password reset email
     */
    public function forgotPassword(string $email): array
    {
        $status = Password::sendResetLink(['email' => $email]);

        if ($status === Password::RESET_LINK_SENT) {
            return [
                'success' => true,
                'message' => 'Email de réinitialisation envoyé',
            ];
        }

        throw ValidationException::withMessages([
            'email' => __($status),
        ]);
    }

    /**
     * Reset password with token
     */
    public function resetPassword(array $data): array
    {
        $status = Password::reset($data, function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(\Illuminate\Support\Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        });

        if ($status === Password::PASSWORD_RESET) {
            return [
                'success' => true,
                'message' => 'Mot de passe réinitialisé avec succès',
            ];
        }

        throw ValidationException::withMessages([
            'email' => __($status),
        ]);
    }

    /**
     * Get redirect URL based on user role
     */
    public function getRedirectUrlByRole(User $user): string
    {
        $role = $user->roles->first()?->name;

        return match($role) {
            'admin' => '/admin/dashboard',
            'responsable_rh' => '/rh/dashboard',
            'responsable_validation' => '/validation/dashboard',
            'bibliothecaire' => '/bibliothecaire/dashboard',
            default => '/dashboard',
        };
    }
}
