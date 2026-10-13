<?php

namespace Modules\Virtualcard\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Virtualcard\Events\VirtualcardStatusUpdate;

class NotifyUserOnVirtualcardStatusUpdate implements ShouldQueue
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
    public function handle(VirtualcardStatusUpdate $event): void
    {
        $cardData = $event->cardData;

        (new \Modules\Virtualcard\Notifications\Mail\VirtualcardStatusUpdateNotification)->send($cardData, ['type' => 'cardData', 'medium' => 'email']);
        (new \Modules\Virtualcard\Notifications\Sms\VirtualcardStatusUpdateNotification)->send($cardData, ['type' => 'cardData', 'medium' => 'sms']);

    }
}
