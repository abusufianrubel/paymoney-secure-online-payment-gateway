<?php

namespace Modules\Virtualcard\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Virtualcard\Events\WithdrawalStatusUpdate;

class NotifyAdminOnWithdrawalStatusUpdate implements ShouldQueue
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
    public function handle(WithdrawalStatusUpdate $event): void
    {
        $withdrawalDetails = $event->withdrawalDetails;

        (new \Modules\Virtualcard\Notifications\Mail\WithdrawalStatusUpdateNotification)->send($withdrawalDetails, ['type' => 'withdrawalDetails', 'medium' => 'email']);
        (new \Modules\Virtualcard\Notifications\Sms\WithdrawalStatusUpdateNotification)->send($withdrawalDetails, ['type' => 'withdrawalDetails', 'medium' => 'sms']);

    }
}
