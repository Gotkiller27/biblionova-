<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Reference;
use Illuminate\Auth\Access\Response;

class ReferencePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Reference $reference): bool
    {
        return $reference->status === 'published' || $user->hasAnyRole(['admin', 'bibliothecaire']) || $user->id === $reference->uploaded_by;
    }

    public function create(User $user): bool
    {
        return $user->can('create references');
    }

    public function update(User $user, Reference $reference): bool
    {
        return $user->can('update references') || $user->id === $reference->uploaded_by;
    }

    public function delete(User $user, Reference $reference): bool
    {
        return $user->can('delete references');
    }

    public function restore(User $user, Reference $reference): bool
    {
        return $user->can('delete references');
    }

    public function forceDelete(User $user, Reference $reference): bool
    {
        return $user->can('delete references');
    }

    public function download(User $user, Reference $reference): bool
    {
        return $reference->status === 'published' || $user->hasAnyRole(['admin', 'bibliothecaire']);
    }
}
