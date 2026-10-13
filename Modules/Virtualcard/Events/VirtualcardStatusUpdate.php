<?php

namespace Modules\Virtualcard\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class VirtualcardStatusUpdate
{
    use SerializesModels, Dispatchable;

    public $cardData;

    /**
     * Create a new event instance.
     */
    public function __construct($cardData)
    {
        $this->cardData = $cardData;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
