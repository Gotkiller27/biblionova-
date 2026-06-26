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
        return $user->can('create publishers');
    }

    public function update(User $user, Publisher $publisher): bool
    {
        return $user->can('update publishers');
    }

    public function delete(User $user, Publisher $publisher): bool
    {
        return $user->can('delete publishers');
    }

    public function restore(User $user, Publisher $publisher): bool
    {
        return $user->can('delete publishers');
    }

    public function forceDelete(User $user, Publisher $publisher): bool
    {
        return $user->can('delete publishers');
    }
}
