<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Category $category): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('create categories');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->can('update categories');
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->can('delete categories');
    }

    public function restore(User $user, Category $category): bool
    {
        return $user->can('delete categories');
    }

    public function forceDelete(User $user, Category $category): bool
    {
        return $user->can('delete categories');
    }
}
