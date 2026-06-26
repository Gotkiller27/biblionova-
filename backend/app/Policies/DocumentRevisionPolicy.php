<?php

namespace App\Policies;

use App\Models\DocumentRevision;
use App\Models\User;

class DocumentRevisionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Bibliothécaire et admin peuvent voir toutes les révisions
        return $user->hasAnyRole(['bibliothecaire', 'admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DocumentRevision $revision): bool
    {
        // Le bibliothécaire auteur peut voir sa révision
        if ($user->id === $revision->bibliothecaire_id) {
            return true;
        }

        // Admin peut voir toutes les révisions
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Seul le bibliothécaire peut créer des révisions
        return $user->hasRole('bibliothecaire');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DocumentRevision $revision): bool
    {
        // Seul le bibliothécaire auteur peut modifier sa révision
        return $user->id === $revision->bibliothecaire_id && $user->hasRole('bibliothecaire');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DocumentRevision $revision): bool
    {
        // Admin peut supprimer n'importe quelle révision
        if ($user->hasRole('admin')) {
            return true;
        }

        // Le bibliothécaire auteur peut supprimer sa propre révision
        return $user->id === $revision->bibliothecaire_id && $user->hasRole('bibliothecaire');
    }
}
