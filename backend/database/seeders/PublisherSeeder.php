<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PublisherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $publishers = [
            ['name' => 'O\'Reilly Media', 'description' => 'Éditeur technique réputé', 'country' => 'États-Unis', 'website' => 'https://oreilly.com'],
            ['name' => 'Dunod', 'description' => 'Éditeur français scientifique', 'country' => 'France', 'website' => 'https://dunod.com'],
            ['name' => 'Manning Publications', 'description' => 'Éditeur de livres techniques', 'country' => 'États-Unis', 'website' => 'https://manning.com'],
        ];

        foreach ($publishers as $publisher) {
            \App\Models\Publisher::firstOrCreate(
                ['name' => $publisher['name']],
                $publisher
            );
        }

        // Créer des éditeurs aléatoires
        \App\Models\Publisher::factory(5)->create();
    }
}
