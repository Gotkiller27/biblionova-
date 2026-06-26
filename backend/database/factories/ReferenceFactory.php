<?php

namespace Database\Factories;

use App\Models\Reference;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reference>
 */
class ReferenceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'subtitle' => fake()->optional()->sentence(6),
            'abstract' => fake()->optional()->paragraph(3),
            'isbn' => fake()->optional()->isbn13(),
            'publication_year' => fake()->optional()->year(),
            'language' => fake()->randomElement(['fr', 'en', 'other']),
            'document_type' => fake()->randomElement(['livre', 'memoire', 'these', 'article', 'revue', 'rapport', 'guide', 'autre']),
            'category_id' => null,
            'publisher_id' => null,
            'uploaded_by' => User::inRandomOrder()->first()?->id,
            'bibliothecaire_id' => User::inRandomOrder()->first()?->id,
            'cover_image' => null,
            'file_path' => null,
            'pages' => fake()->optional()->numberBetween(50, 800),
            'download_count' => fake()->numberBetween(0, 500),
            'view_count' => fake()->numberBetween(50, 2000),
            'status' => fake()->randomElement(['draft', 'published', 'published', 'published', 'archived']),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }
}
