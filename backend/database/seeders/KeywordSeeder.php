<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KeywordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $keywords = [
            ['name' => 'Laravel'],
            ['name' => 'PHP'],
            ['name' => 'JavaScript'],
            ['name' => 'Vue.js'],
            ['name' => 'Machine Learning'],
            ['name' => 'Python'],
            ['name' => 'SQL'],
            ['name' => 'API'],
        ];

        foreach ($keywords as $keyword) {
            \App\Models\Keyword::firstOrCreate(
                ['name' => $keyword['name']],
                $keyword
            );
        }

        // Créer des mots-clés aléatoires
        \App\Models\Keyword::factory(15)->create();
    }
}
