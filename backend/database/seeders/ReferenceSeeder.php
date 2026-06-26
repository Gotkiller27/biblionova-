<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\Reference;
use App\Models\ReferenceKeyword;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReferenceSeeder extends Seeder
{
    public function run(): void
    {
        Reference::factory(2)->published()->create([
            'title' => 'Introduction to Laravel',
            'language' => 'fr',
            'document_type' => 'livre',
            'category_id' => Category::inRandomOrder()->first()->id,
            'publisher_id' => Publisher::inRandomOrder()->first()->id,
            'uploaded_by' => User::inRandomOrder()->first()->id,
        ])->each(function (Reference $reference) {
            $reference->authors()->sync(Author::inRandomOrder()->take(rand(1, 3))->pluck('id'));
            ReferenceKeyword::factory(5)->create(['reference_id' => $reference->id]);
        });

        Reference::factory(30)->create([
            'category_id' => Category::inRandomOrder()->first()->id,
            'publisher_id' => Publisher::inRandomOrder()->first()->id,
            'uploaded_by' => User::inRandomOrder()->first()->id,
        ])->each(function (Reference $reference) {
            $reference->authors()->sync(Author::inRandomOrder()->take(rand(1, 3))->pluck('id'));
            ReferenceKeyword::factory(3)->create(['reference_id' => $reference->id]);
        });
    }
}
