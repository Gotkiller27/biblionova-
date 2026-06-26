<?php

namespace App\Listeners;

use App\Events\DepositRequestReviewed;
use App\Services\NotificationService;

class SendDepositReviewedNotification
{
    public function __construct(private NotificationService $notificationService) {}

    public function handle(DepositRequestReviewed $event): void
    {
        if ($event->decision === 'approve') {
            $this->notificationService->notifyDepositApproved($event->depositRequest);
        } elseif ($event->decision === 'reject') {
            $this->notificationService->notifyDepositRejected($event->depositRequest);
        } elseif ($event->decision === 'second_review') {
            $this->notificationService->notifySecondReviewNeeded($event->depositRequest);
        }
    }
}
