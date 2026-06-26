<?php

namespace App\Policies;

use App\Models\DepositRequestReview;
use App\Models\User;

class DepositRequestReviewPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin peut voir toutes les reviews
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DepositRequestReview $review): bool
    {
        // Le reviewer peut voir sa propre review
        if ($user->id === $review->reviewer_id) {
            return true;
        }

        // L'applicant peut voir les reviews de sa demande
        if ($user->id === $review->depositRequest->applicant_id) {
            return true;
        }

        // Admin peut voir toutes les reviews
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Responsable de validation et admin peuvent créer des reviews
        return $user->hasAnyRole(['responsable_validation', 'admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DepositRequestReview $review): bool
    {
        // Seul le reviewer peut modifier sa propre review
        return $user->id === $review->reviewer_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DepositRequestReview $review): bool
    {
        // Seul l'admin peut supprimer une review
        return $user->hasRole('admin');
    }
}
