<?php

namespace App\Events;

use App\Models\Reference;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReferencePublished
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reference;

    public function __construct(Reference $reference)
    {
        $this->reference = $reference;
    }
}
