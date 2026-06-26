<?php

namespace Database\Factories;

use App\Models\DepositRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepositRequestReviewFactory extends Factory
{
    public function definition(): array
    {
        $reviewer = User::factory()->responsableDemande();
        return [
            'deposit_request_id' => DepositRequest::factory(),
            'reviewer_id' => $reviewer,
            'reviewer_role' => 'responsable_demande',
            'decision' => fake()->randomElement(['approved', 'rejected', 'override', 'second_opinion_requested']),
            'justification' => fake()->paragraph(),
        ];
    }
}
