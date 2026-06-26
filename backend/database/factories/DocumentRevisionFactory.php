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
        // 1. On cherche un bibliothécaire existant
        $bibliothecaire = User::whereHas('roles', fn($q) => $q->where('name', 'bibliothecaire'))
            ->inRandomOrder()
            ->first();

        // 2. Si aucun n'existe, on le crée à la volée et on lui donne le rôle Spatie
        if (!$bibliothecaire) {
            $bibliothecaire = User::factory()->create();
            $bibliothecaire->assignRole('bibliothecaire');
        }

        return [
            // Sécurisation aussi du reference_id si la table est vide
            'reference_id' => Reference::inRandomOrder()->first()?->id ?? Reference::factory(),
            'bibliothecaire_id' => $bibliothecaire->id,
            'action' => fake()->randomElement(['creation', 'modification', 'correction', 'archivage']),
            'commentaire' => fake()->paragraph(2), // Changé en paragraph pour avoir du contenu sympa
        ];
    }
}