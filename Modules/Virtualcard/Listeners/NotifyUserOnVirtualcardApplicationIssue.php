<?php

namespace Modules\Virtualcard\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Virtualcard\Events\VirtualcardApplicationIssue;

class NotifyUserOnVirtualcardApplicationIssue implements ShouldQueue
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
    public function handle(VirtualcardApplicationIssue $event): void
    {
        $applicationData = $event->applicationData;

        (new \Modules\Virtualcard\Notifications\Mail\VirtualcardApplicationIssueNotification)->send($applicationData, ['type' => 'applicationData', 'medium' => 'email']);
        (new \Modules\Virtualcard\Notifications\Sms\VirtualcardApplicationIssueNotification)->send($applicationData, ['type' => 'applicationData', 'medium' => 'sms']);

    }
}
