<?php

namespace Database\Factories;

use App\Models\DepositRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepositRequestFactory extends Factory
{
    protected $model = DepositRequest::class;

    public function definition(): array
    {
        return [
            'applicant_id' => User::factory(),
            // On utilise une factory simple sans le state ->responsableDemande() qui bugge
            'assigned_manager_id' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(3),
            'proposed_file' => fake()->filePath(),
            'status' => fake()->randomElement(['pending', 'approved_by_manager', 'rejected_by_manager', 'second_review', 'approved', 'rejected', 'published']),
        ];
    }

    /**
     * Configuration pour assigner les rôles Spatie après la création en base
     */
    public function configure(): static
    {
        return $this->afterCreating(function (DepositRequest $depositRequest) {
            // Assigne le rôle utilisateur au demandeur
            if ($depositRequest->applicant) {
                $depositRequest->applicant->assignRole('utilisateur');
            }

            // Assigne le rôle responsable_demande au manager assigné
            if ($depositRequest->assignedManager) {
                $depositRequest->assignedManager->assignRole('responsable_demande');
            }
        });
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'pending']);
    }
}