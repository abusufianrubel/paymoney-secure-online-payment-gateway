<?php

namespace Modules\Virtualcard\Listeners;

use Modules\Virtualcard\Events\WithdrawalRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyAdminOnWithdrawalRequest implements ShouldQueue
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
    public function handle(WithdrawalRequest $event): void
    {
        $withdrawalData = $event->withdrawalData;

        (new \Modules\Virtualcard\Notifications\Mail\WithdrawalRequestNotification)->send($withdrawalData, ['type' => 'withdrawalRequest', 'medium' => 'email']);

    }
}
