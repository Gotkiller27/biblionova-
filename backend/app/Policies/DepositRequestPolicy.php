<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DepositRequest;
use Illuminate\Auth\Access\Response;

class DepositRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'responsable_validation']);
    }

    public function view(User $user, DepositRequest $depositRequest): bool
    {
        return $user->id === $depositRequest->applicant_id || $user->hasAnyRole(['admin', 'responsable_validation']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['utilisateur', 'bibliothecaire', 'admin']);
    }

    public function update(User $user, DepositRequest $depositRequest): bool
    {
        return $user->id === $depositRequest->applicant_id && in_array($depositRequest->status, ['pending', 'second_review']);
    }

    public function delete(User $user, DepositRequest $depositRequest): bool
    {
        return $user->id === $depositRequest->applicant_id || $user->hasRole('admin');
    }

    public function review(User $user, DepositRequest $depositRequest): bool
    {
        return $user->hasAnyRole(['admin', 'responsable_validation']);
    }
}
