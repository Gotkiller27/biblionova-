<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Favorite;
use Illuminate\Auth\Access\Response;

class FavoritePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Favorite $favorite): bool
    {
        return $user->id === $favorite->user_id || $user->hasAnyRole(['admin', 'bibliothecaire']);
    }

    public function create(User $user): bool
    {
        return $user->can('manage favorites');
    }

    public function update(User $user, Favorite $favorite): bool
    {
        return $user->id === $favorite->user_id;
    }

    public function delete(User $user, Favorite $favorite): bool
    {
        return $user->id === $favorite->user_id || $user->hasAnyRole(['admin', 'bibliothecaire']);
    }
}
