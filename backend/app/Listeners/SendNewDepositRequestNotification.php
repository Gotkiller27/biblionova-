<?php

namespace App\Listeners;

use App\Events\DepositRequestCreated;
use App\Services\NotificationService;

class SendNewDepositRequestNotification
{
    public function __construct(private NotificationService $notificationService) {}

    public function handle(DepositRequestCreated $event): void
    {
        $this->notificationService->notifyNewDepositRequest($event->depositRequest);
    }
}
