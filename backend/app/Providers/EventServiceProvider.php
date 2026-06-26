<?php

namespace App\Providers;

use App\Events\DepositRequestCreated;
use App\Events\DepositRequestReviewed;
use App\Events\ReferencePublished;
use App\Listeners\SendNewDepositRequestNotification;
use App\Listeners\SendDepositReviewedNotification;
use App\Listeners\SendReferencePublishedNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        DepositRequestCreated::class => [
            SendNewDepositRequestNotification::class,
        ],
        DepositRequestReviewed::class => [
            SendDepositReviewedNotification::class,
        ],
        ReferencePublished::class => [
            SendReferencePublishedNotification::class,
        ],
    ];

    public function boot()
    {
        //
    }
}
