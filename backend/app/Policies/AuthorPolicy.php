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
        return $user->can('create authors');
    }

    public function update(User $user, Author $author): bool
    {
        return $user->can('update authors');
    }

    public function delete(User $user, Author $author): bool
    {
        return $user->can('delete authors');
    }

    public function restore(User $user, Author $author): bool
    {
        return $user->can('delete authors');
    }

    public function forceDelete(User $user, Author $author): bool
    {
        return $user->can('delete authors');
    }
}
