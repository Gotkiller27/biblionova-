<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Author;
use Illuminate\Auth\Access\Response;

class AuthorPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Author $author): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'bibliothecaire']);
    }

    public function update(User $user, Author $author): bool
    {
        return $user->hasRole(['admin', 'bibliothecaire']);
    }

    public function delete(User $user, Author $author): bool
    {
        // Prevent deletion if author has references
        if ($author->references()->count() > 0) {
            return false;
        }
        return $user->hasRole(['admin', 'bibliothecaire']);
    }

    public function restore(User $user, Author $author): bool
    {
        return $user->hasRole(['admin', 'bibliothecaire']);
    }

    public function forceDelete(User $user, Author $author): bool
    {
        // Only admin can force delete
        if (!$user->hasRole('admin')) {
            return false;
        }
        // Prevent force deletion if author has references
        if ($author->references()->withTrashed()->count() > 0) {
            return false;
        }
        return true;
    }
}
