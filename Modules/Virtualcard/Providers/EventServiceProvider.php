<?php

namespace Modules\Virtualcard\Providers;

use Modules\Virtualcard\Events\TopupRequest;
use Modules\Virtualcard\Listeners\NotifyAdminOnTopupRequest;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use Modules\Virtualcard\Events\{
    TopupStatusUpdate,
    WithdrawalRequest,
    VirtualcardRequest,
    VirtualcardApplication,
    WithdrawalStatusUpdate,
    VirtualcardStatusUpdate,
    VirtualcardApplicationIssue,
    VirtualcardApplicationApproveReject
};
use Modules\Virtualcard\Listeners\{
    NotifyUserOnTopupStatusUpdate,
    NotifyAdminOnWithdrawalRequest,
    NotifyAdminOnVirtualcardRequest,
    NotifyAdminOnWithdrawalStatusUpdate,
    NotifyUserOnVirtualcardStatusUpdate,
    NotifyAdminOnVirtualcardApplication,
    NotifyUserOnVirtualcardApplicationIssue,
    NotifyUserOnVirtualcardApplicationApproveReject
};

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        TopupRequest::class => [
            NotifyAdminOnTopupRequest::class,
        ],
        TopupStatusUpdate::class => [
            NotifyUserOnTopupStatusUpdate::class,
        ],
        VirtualcardApplication::class => [
            NotifyAdminOnVirtualcardApplication::class,
        ],
        VirtualcardRequest::class => [
            NotifyAdminOnVirtualcardRequest::class,
        ],
        VirtualcardApplicationIssue::class => [
            NotifyUserOnVirtualcardApplicationIssue::class,
        ],
        VirtualcardApplicationApproveReject::class => [
            NotifyUserOnVirtualcardApplicationApproveReject::class,
        ],
        WithdrawalRequest::class => [
            NotifyAdminOnWithdrawalRequest::class,
        ],
        WithdrawalStatusUpdate::class => [
            NotifyAdminOnWithdrawalStatusUpdate::class,
        ],
        VirtualcardStatusUpdate::class => [
            NotifyUserOnVirtualcardStatusUpdate::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
