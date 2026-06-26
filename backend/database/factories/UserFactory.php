<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'password' => static::$password ??= Hash::make('password'),
            'status' => fake()->randomElement(['active', 'inactive', 'suspended']),
            'email_verified_at' => now(),
            'last_login_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function admin(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('admin');
        });
    }

    public function responsableRh(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('responsable_rh');
        });
    }

    public function responsableDemande(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('responsable_demande');
        });
    }

    public function user(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('utilisateur');
        });
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'active']);
    }
}