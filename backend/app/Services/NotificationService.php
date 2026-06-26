<?php

namespace App\Services;

use App\Models\User;
use App\Models\DepositRequest;
use App\Models\Reference;
use App\Notifications\NewDepositRequestNotification;
use App\Notifications\DepositApprovedNotification;
use App\Notifications\DepositRejectedNotification;
use App\Notifications\SecondReviewNotification;
use App\Notifications\ReferencePublishedNotification;

class NotificationService
{
    public function notifyNewDepositRequest(DepositRequest $depositRequest): void
    {
        $admins = User::role(['admin', 'responsable_validation'])->get();
        foreach ($admins->each->notify(new NewDepositRequestNotification($depositRequest)));
    }

    public function notifyDepositApproved(DepositRequest $depositRequest): void
    {
        $depositRequest->applicant->notify(new DepositApprovedNotification($depositRequest));
    }

    public function notifyDepositRejected(DepositRequest $depositRequest): void
    {
        $depositRequest->applicant->notify(new DepositRejectedNotification($depositRequest));
    }

    public function notifySecondReviewNeeded(DepositRequest $depositRequest): void
    {
        $admins = User::role(['admin'])->get();
        foreach ($admins->each->notify(new SecondReviewNotification($depositRequest)));
    }

    public function notifyReferencePublished(Reference $reference): void
    {
        $users = User::role(['utilisateur'])->get();
        foreach ($users->each->notify(new ReferencePublishedNotification($reference)));
    }
}
