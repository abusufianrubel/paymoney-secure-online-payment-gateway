<?php

namespace Modules\Virtualcard\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Virtualcard\Events\VirtualcardApplicationApproveReject;

class NotifyUserOnVirtualcardApplicationApproveReject implements ShouldQueue
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
    public function handle(VirtualcardApplicationApproveReject $event): void
    {
        $applicationData = $event->applicationData;

        (new \Modules\Virtualcard\Notifications\Mail\VirtualcardApplicationApproveRejectNotification)->send($applicationData, ['type' => 'applicationData', 'medium' => 'email']);
        (new \Modules\Virtualcard\Notifications\Sms\VirtualcardApplicationApproveRejectNotification)->send($applicationData, ['type' => 'applicationData', 'medium' => 'sms']);

    }
}
