<?php

namespace Modules\Virtualcard\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Virtualcard\Events\TopupStatusUpdate;

class NotifyUserOnTopupStatusUpdate implements ShouldQueue
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
    public function handle(TopupStatusUpdate $event): void
    {
        $topupDetails = $event->topupDetails;

        (new \Modules\Virtualcard\Notifications\Mail\TopupStatusUpdateNotification)->send($topupDetails, ['type' => 'topupDetails', 'medium' => 'email']);
        (new \Modules\Virtualcard\Notifications\Sms\TopupStatusUpdateNotification)->send($topupDetails, ['type' => 'topupDetails', 'medium' => 'sms']);

    }
}
