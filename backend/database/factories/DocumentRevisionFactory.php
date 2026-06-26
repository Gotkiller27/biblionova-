<?php

namespace Database\Factories;

use App\Models\DocumentRevision;
use App\Models\Reference;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentRevision>
 */
class DocumentRevisionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reference_id' => Reference::inRandomOrder()->first()?->id,
            'bibliothecaire_id' => User::whereHas('roles', fn($q) => $q->where('name', 'bibliothecaire'))->inRandomOrder()->first()?->id,
            'action' => fake()->randomElement(['creation', 'modification', 'correction', 'archivage']),
            'commentaire' => fake()->optional()->sentence(),
        ];
    }
}
