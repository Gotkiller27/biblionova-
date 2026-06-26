<?php

namespace App\Listeners;

use App\Events\ReferencePublished;
use App\Services\NotificationService;

class SendReferencePublishedNotification
{
    public function __construct(private NotificationService $notificationService) {}

    public function handle(ReferencePublished $event): void
    {
        $this->notificationService->notifyReferencePublished($event->reference);
    }
}
