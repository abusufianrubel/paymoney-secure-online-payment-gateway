<?php

namespace Modules\Virtualcard\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class WithdrawalRequest
{
    use SerializesModels, Dispatchable;

    public $withdrawalData;

    /**
     * Create a new event instance.
     */
    public function __construct($withdrawalData)
    {
        $this->withdrawalData = $withdrawalData;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
