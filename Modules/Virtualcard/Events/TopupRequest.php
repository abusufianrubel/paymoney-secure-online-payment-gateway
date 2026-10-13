<?php

namespace Modules\Virtualcard\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class TopupRequest
{
    use SerializesModels, Dispatchable;

    public $topup;

    /**
     * Create a new event instance.
     */
    public function __construct($topup)
    {
        $this->topup = $topup;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
