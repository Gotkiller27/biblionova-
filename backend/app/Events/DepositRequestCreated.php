<?php

namespace App\Events;

use App\Models\DepositRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DepositRequestCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $depositRequest;

    public function __construct(DepositRequest $depositRequest)
    {
        $this->depositRequest = $depositRequest;
    }
}
