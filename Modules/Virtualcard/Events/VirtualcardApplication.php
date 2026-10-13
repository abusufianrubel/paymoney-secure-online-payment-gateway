<?php

namespace Modules\Virtualcard\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class VirtualcardApplication
{
    use SerializesModels, Dispatchable;

    public $applicationData;

    /**
     * Create a new event instance.
     */
    public function __construct($applicationData)
    {
        $this->applicationData = $applicationData;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
