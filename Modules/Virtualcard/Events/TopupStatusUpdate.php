<?php

namespace Modules\Virtualcard\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class TopupStatusUpdate
{
    use SerializesModels, Dispatchable;

    public $topupDetails;

    /**
     * Create a new event instance.
     */
    public function __construct($topupDetails)
    {
        $this->topupDetails = $topupDetails;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
