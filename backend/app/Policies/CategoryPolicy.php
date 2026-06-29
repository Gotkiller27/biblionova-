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
        return $user->hasRole(['admin', 'bibliothecaire']);
    }

    public function update(User $user, Category $category): bool
    {
        return $user->hasRole(['admin', 'bibliothecaire']);
    }

    public function delete(User $user, Category $category): bool
    {
        // Prevent deletion if category has references
        if ($category->references()->exists() || $category->children()->exists()) {
            return false;
        }
        
        return $user->hasRole(['admin', 'bibliothecaire']);
    }

    public function restore(User $user, Category $category): bool
    {
        return $user->hasRole(['admin', 'bibliothecaire']);
    }

    public function forceDelete(User $user, Category $category): bool
    {
        // Only admin can force delete
        if (!$user->hasRole('admin')) {
            return false;
        }
        
        // Prevent force deletion if category has references
        if ($category->references()->withTrashed()->exists() || $category->children()->withTrashed()->exists()) {
            return false;
        }
        
        return true;
    }
}

