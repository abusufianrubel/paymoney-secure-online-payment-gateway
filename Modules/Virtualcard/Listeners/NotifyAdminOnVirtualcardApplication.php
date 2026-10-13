<?php

namespace Modules\Virtualcard\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Virtualcard\Events\VirtualcardApplication;

class NotifyAdminOnVirtualcardApplication implements ShouldQueue
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
    public function handle(VirtualcardApplication $event): void
    {
        $applicationData = $event->applicationData;

        (new \Modules\Virtualcard\Notifications\Mail\VirtualcardApplicationNotification)->send($applicationData, ['type' => 'applicationData', 'medium' => 'email']);

    }
}
