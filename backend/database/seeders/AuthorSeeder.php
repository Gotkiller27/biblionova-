<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authors = [
            ['first_name' => 'John', 'last_name' => 'Doe', 'biography' => 'Expert en développement web', 'nationality' => 'Américain', 'birth_date' => '1980-01-15'],
            ['first_name' => 'Jane', 'last_name' => 'Smith', 'biography' => 'Spécialiste en intelligence artificielle', 'nationality' => 'Britannique', 'birth_date' => '1985-05-22'],
            ['first_name' => 'Pierre', 'last_name' => 'Martin', 'biography' => 'Auteur français spécialisé en informatique', 'nationality' => 'Français', 'birth_date' => '1975-11-30'],
        ];

        foreach ($authors as $author) {
            \App\Models\Author::firstOrCreate(
                ['first_name' => $author['first_name'], 'last_name' => $author['last_name']],
                $author
            );
        }

        // Créer des auteurs aléatoires
        \App\Models\Author::factory(10)->create();
    }
}
