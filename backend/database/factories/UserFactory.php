<?php

namespace Database\Factories;

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
            'role' => fake()->randomElement(['admin', 'responsable_rh', 'responsable_demande', 'user']),
            'status' => fake()->randomElement(['active', 'inactive', 'suspended']),
            'email_verified_at' => now(),
            'last_login_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'admin']);
    }

    public function responsableRh(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'responsable_rh']);
    }

    public function responsableDemande(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'responsable_demande']);
    }

    public function user(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'user']);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'active']);
    }
}
