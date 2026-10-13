<?php

namespace Modules\Virtualcard\Listeners;

use Modules\Virtualcard\Events\TopupRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyAdminOnTopupRequest implements ShouldQueue
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
    public function handle(TopupRequest $event): void
    {
        $topup = $event->topup;

        (new \Modules\Virtualcard\Notifications\Mail\TopupRequestNotification)->send($topup, ['type' => 'topup', 'medium' => 'email']);

    }
}
