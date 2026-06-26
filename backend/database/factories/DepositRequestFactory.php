<?php

namespace Database\Factories;

use App\Models\DepositRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepositRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'applicant_id' => User::factory(),
            'assigned_manager_id' => User::factory()->responsableDemande(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(3),
            'proposed_file' => fake()->filePath(),
            'status' => fake()->randomElement(['pending', 'approved_by_manager', 'rejected_by_manager', 'second_review', 'approved', 'rejected', 'published']),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'pending']);
    }
}
