<?php

namespace Database\Seeders;

use App\Models\DepositRequest;
use App\Models\DepositRequestReview;
use App\Models\DocumentRevision;
use App\Models\Reference;
use App\Models\User;
use Illuminate\Database\Seeder;

class DepositWorkflowSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les utilisateurs par rôle
        $users = User::whereHas('roles', fn($q) => $q->where('name', 'user'))->limit(5)->get();
        $responsables = User::whereHas('roles', fn($q) => $q->where('name', 'responsable_validation'))->limit(3)->get();
        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->limit(2)->get();
        $bibliothecaires = User::whereHas('roles', fn($q) => $q->where('name', 'bibliothecaire'))->limit(2)->get();

        if ($users->isEmpty() || $responsables->isEmpty() || $admins->isEmpty() || $bibliothecaires->isEmpty()) {
            $this->command->warn('Certains rôles n\'ont pas d\'utilisateurs. Assurez-vous d\'exécuter les seeders d\'utilisateurs d\'abord.');
            return;
        }

        // 1. Créer des demandes en attente
        $this->command->info('Création des demandes en attente...');
        foreach ($users->take(3) as $user) {
            DepositRequest::create([
                'applicant_id' => $user->id,
                'title' => fake()->sentence(4),
                'description' => fake()->paragraph(),
                'status' => 'pending',
            ]);
        }

        // 2. Créer des demandes assignées à un responsable
        $this->command->info('Création des demandes assignées...');
        foreach ($users->skip(1)->take(2) as $user) {
            DepositRequest::create([
                'applicant_id' => $user->id,
                'assigned_manager_id' => $responsables->random()->id,
                'title' => fake()->sentence(4),
                'description' => fake()->paragraph(),
                'status' => 'pending',
            ]);
        }

        // 3. Créer des demandes approuvées par un responsable
        $this->command->info('Création des demandes approuvées par responsable...');
        foreach ($users->take(2) as $user) {
            $depositRequest = DepositRequest::create([
                'applicant_id' => $user->id,
                'assigned_manager_id' => $responsables->first()->id,
                'title' => fake()->sentence(4),
                'description' => fake()->paragraph(),
                'status' => 'approved_by_manager',
            ]);

            // Créer la review
            DepositRequestReview::create([
                'deposit_request_id' => $depositRequest->id,
                'reviewer_id' => $responsables->first()->id,
                'reviewer_role' => 'responsable_validation',
                'decision' => 'approve',
                'justification' => fake()->paragraph(),
            ]);
        }

        // 4. Créer des demandes rejetées
        $this->command->info('Création des demandes rejetées...');
        $depositRequest = DepositRequest::create([
            'applicant_id' => $users->random()->id,
            'assigned_manager_id' => $responsables->random()->id,
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => 'rejected_by_manager',
        ]);

        DepositRequestReview::create([
            'deposit_request_id' => $depositRequest->id,
            'reviewer_id' => $responsables->random()->id,
            'reviewer_role' => 'responsable_validation',
            'decision' => 'reject',
            'justification' => 'Le document ne respecte pas les critères de qualité.',
        ]);

        // 5. Créer des demandes en second avis
        $this->command->info('Création des demandes en second avis...');
        $depositRequest = DepositRequest::create([
            'applicant_id' => $users->random()->id,
            'assigned_manager_id' => $responsables->random()->id,
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => 'second_review',
        ]);

        DepositRequestReview::create([
            'deposit_request_id' => $depositRequest->id,
            'reviewer_id' => $responsables->random()->id,
            'reviewer_role' => 'responsable_validation',
            'decision' => 'second_review',
            'justification' => 'Dossier complexe nécessitant un avis administratif.',
        ]);

        // 6. Créer des demandes approuvées par admin
        $this->command->info('Création des demandes approuvées...');
        foreach ($users->take(2) as $user) {
            $depositRequest = DepositRequest::create([
                'applicant_id' => $user->id,
                'assigned_manager_id' => $responsables->random()->id,
                'title' => fake()->sentence(4),
                'description' => fake()->paragraph(),
                'status' => 'approved',
            ]);

            // Review du responsable
            DepositRequestReview::create([
                'deposit_request_id' => $depositRequest->id,
                'reviewer_id' => $responsables->random()->id,
                'reviewer_role' => 'responsable_validation',
                'decision' => 'approve',
                'justification' => fake()->paragraph(),
            ]);

            // Review de l'admin
            DepositRequestReview::create([
                'deposit_request_id' => $depositRequest->id,
                'reviewer_id' => $admins->first()->id,
                'reviewer_role' => 'admin',
                'decision' => 'approve',
                'justification' => fake()->paragraph(),
            ]);
        }

        // 7. Créer des demandes publiées
        $this->command->info('Création des demandes publiées...');
        $references = Reference::limit(3)->get();
        
        if ($references->isNotEmpty()) {
            foreach ($references as $reference) {
                $depositRequest = DepositRequest::create([
                    'applicant_id' => $users->random()->id,
                    'assigned_manager_id' => $responsables->random()->id,
                    'title' => $reference->title,
                    'description' => fake()->paragraph(),
                    'status' => 'published',
                ]);

                // Reviews
                DepositRequestReview::create([
                    'deposit_request_id' => $depositRequest->id,
                    'reviewer_id' => $responsables->random()->id,
                    'reviewer_role' => 'responsable_validation',
                    'decision' => 'approve',
                    'justification' => fake()->paragraph(),
                ]);

                DepositRequestReview::create([
                    'deposit_request_id' => $depositRequest->id,
                    'reviewer_id' => $admins->first()->id,
                    'reviewer_role' => 'admin',
                    'decision' => 'approve',
                    'justification' => fake()->paragraph(),
                ]);

                // Créer une révision documentaire
                DocumentRevision::create([
                    'reference_id' => $reference->id,
                    'bibliothecaire_id' => $bibliothecaires->random()->id,
                    'action' => 'creation',
                    'commentaire' => 'Document créé suite à la demande de dépôt #' . $depositRequest->id,
                ]);
            }
        }

        // 8. Créer des révisions documentaires supplémentaires
        $this->command->info('Création des révisions documentaires...');
        $allReferences = Reference::limit(10)->get();
        
        if ($allReferences->isNotEmpty()) {
            foreach ($allReferences as $reference) {
                // Révision de modification
                DocumentRevision::create([
                    'reference_id' => $reference->id,
                    'bibliothecaire_id' => $bibliothecaires->random()->id,
                    'action' => 'modification',
                    'commentaire' => fake()->sentence(),
                ]);

                // Révision de correction (optional)
                if (fake()->boolean(30)) {
                    DocumentRevision::create([
                        'reference_id' => $reference->id,
                        'bibliothecaire_id' => $bibliothecaires->random()->id,
                        'action' => 'correction',
                        'commentaire' => 'Correction des métadonnées',
                    ]);
                }
            }
        }

        $this->command->info('✓ Workflow métier créé avec succès !');
    }
}
