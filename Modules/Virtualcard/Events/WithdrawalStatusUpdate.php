<?php

namespace Modules\Virtualcard\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class WithdrawalStatusUpdate
{
    use SerializesModels, Dispatchable;

    public $withdrawalDetails;

    /**
     * Create a new event instance.
     */
    public function __construct($withdrawalDetails)
    {
        $this->withdrawalDetails = $withdrawalDetails;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
