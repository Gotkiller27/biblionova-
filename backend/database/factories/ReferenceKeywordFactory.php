<?php

namespace Database\Factories;

use App\Models\Reference;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReferenceKeywordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reference_id' => Reference::factory(),
            'keyword' => fake()->word(),
        ];
    }
}
