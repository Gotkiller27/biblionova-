<?php

namespace Database\Factories;

use App\Models\Reference;
use App\Models\User;
use App\Models\View;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<View>
 */
class ViewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => fake()->optional()->randomElement(User::pluck('id')->toArray()),
            'reference_id' => Reference::inRandomOrder()->first()?->id,
            'viewed_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
