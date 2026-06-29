<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Keyword;
use Illuminate\Auth\Access\Response;

class KeywordPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Keyword $keyword): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'bibliothecaire']);
    }

    public function update(User $user, Keyword $keyword): bool
    {
        return $user->hasRole(['admin', 'bibliothecaire']);
    }

    public function delete(User $user, Keyword $keyword): bool
    {
        // Prevent deletion if keyword has references
        if ($keyword->references()->count() > 0) {
            return false;
        }
        return $user->hasRole(['admin', 'bibliothecaire']);
    }

    public function restore(User $user, Keyword $keyword): bool
    {
        return $user->hasRole(['admin', 'bibliothecaire']);
    }

    public function forceDelete(User $user, Keyword $keyword): bool
    {
        // Only admin can force delete
        if (!$user->hasRole('admin')) {
            return false;
        }
        // Prevent force deletion if keyword has references
        if ($keyword->references()->withTrashed()->count() > 0) {
            return false;
        }
        return true;
    }
}
