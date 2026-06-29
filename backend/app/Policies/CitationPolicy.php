<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Citation;
use Illuminate\Auth\Access\Response;

class CitationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Citation $citation): bool
    {
        return $user->can('view citations');
    }

    public function create(User $user): bool
    {
        return $user->can('manage citations');
    }

    public function update(User $user, Citation $citation): bool
    {
        return $user->can('manage citations');
    }

    public function delete(User $user, Citation $citation): bool
    {
        return $user->can('manage citations');
    }
}
