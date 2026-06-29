<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Publisher;
use Illuminate\Auth\Access\Response;

class PublisherPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Publisher $publisher): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'bibliothecaire']);
    }

    public function update(User $user, Publisher $publisher): bool
    {
        return $user->hasRole(['admin', 'bibliothecaire']);
    }

    public function delete(User $user, Publisher $publisher): bool
    {
        // Prevent deletion if publisher has references
        if ($publisher->references()->count() > 0) {
            return false;
        }
        return $user->hasRole(['admin', 'bibliothecaire']);
    }

    public function restore(User $user, Publisher $publisher): bool
    {
        return $user->hasRole(['admin', 'bibliothecaire']);
    }

    public function forceDelete(User $user, Publisher $publisher): bool
    {
        // Only admin can force delete
        if (!$user->hasRole('admin')) {
            return false;
        }
        // Prevent force deletion if publisher has references
        if ($publisher->references()->withTrashed()->count() > 0) {
            return false;
        }
        return true;
    }
}
