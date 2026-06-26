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
        return $user->can('create keywords');
    }

    public function update(User $user, Keyword $keyword): bool
    {
        return $user->can('update keywords');
    }

    public function delete(User $user, Keyword $keyword): bool
    {
        return $user->can('delete keywords');
    }

    public function restore(User $user, Keyword $keyword): bool
    {
        return $user->can('delete keywords');
    }

    public function forceDelete(User $user, Keyword $keyword): bool
    {
        return $user->can('delete keywords');
    }
}
