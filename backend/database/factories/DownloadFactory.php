<?php

namespace Database\Factories;

use App\Models\Download;
use App\Models\Reference;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Download>
 */
class DownloadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => fake()->optional()->randomElement(User::pluck('id')->toArray()),
            'reference_id' => Reference::inRandomOrder()->first()?->id,
            'downloaded_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
