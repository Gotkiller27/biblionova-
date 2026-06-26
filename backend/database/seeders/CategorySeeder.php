<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Informatique', 'slug' => 'informatique', 'description' => 'Livres et documents sur l\'informatique', 'status' => 'active'],
            ['name' => 'Business', 'slug' => 'business', 'description' => 'Documents sur la gestion d\'entreprise', 'status' => 'active'],
            ['name' => 'Sciences', 'slug' => 'sciences', 'description' => 'Sciences fondamentales et appliquées', 'status' => 'active'],
            ['name' => 'Développement web', 'slug' => 'developpement-web', 'description' => 'Documents sur le développement web', 'parent_id' => 1, 'status' => 'active'],
            ['name' => 'Intelligence artificielle', 'slug' => 'intelligence-artificielle', 'description' => 'IA et machine learning', 'parent_id' => 1, 'status' => 'active'],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        // Créer des catégories aléatoires supplémentaires
        \App\Models\Category::factory(5)->create();
    }
}
