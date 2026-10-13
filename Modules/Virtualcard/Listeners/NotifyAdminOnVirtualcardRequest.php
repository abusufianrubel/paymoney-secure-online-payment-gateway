<?php

namespace Modules\Virtualcard\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Virtualcard\Events\VirtualcardRequest;

class NotifyAdminOnVirtualcardRequest implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(VirtualcardRequest $event): void
    {
        $applicationData = $event->applicationData;

        (new \Modules\Virtualcard\Notifications\Mail\VirtualcardRequestNotification)->send($applicationData, ['type' => 'applicationData', 'medium' => 'email']);

    }
}
