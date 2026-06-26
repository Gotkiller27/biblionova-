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

        // Auto login after registration
        Auth::login($user);

        // Regenerate session for security
        request()->session()->regenerate();

        return [
            'success' => true,
            'message' => 'Inscription réussie',
            'user' => $user->load('roles'),
        ];
    }

    /**
     * Login user with session-based authentication
     */
    public function login(array $credentials): array
    {
        // Extract remember me option
        $remember = $credentials['remember'] ?? false;
        unset($credentials['remember']);

        // Attempt login
        if (!Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Les informations d\'identification sont incorrectes.',
            ]);
        }

        $user = Auth::user();

        // Check if account is active
        if ($user->status !== 'active') {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Votre compte est ' . $user->status . '.',
            ]);
        }

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Regenerate session for security
        request()->session()->regenerate();

        return [
            'success' => true,
            'message' => 'Connexion réussie',
            'user' => $user->load('roles'),
        ];
    }

    /**
     * Logout user
     */
    public function logout(): array
    {
        Auth::logout();
        
        // Invalidate session
        request()->session()->invalidate();
        
        // Regenerate CSRF token
        request()->session()->regenerateToken();

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
