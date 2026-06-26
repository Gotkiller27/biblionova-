<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Author>
 */
class AuthorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'biography' => fake()->optional()->paragraph(),
            'nationality' => fake()->optional()->country(),
            'birth_date' => fake()->optional()->dateTimeBetween('-70 years', '-20 years'),
            'death_date' => fake()->optional()->dateTimeBetween('-30 years', '-5 years'),
        ];
    }
}
