<?php

use Illuminate\Support\Facades\Route;
use Modules\Virtualcard\Http\Controllers\Admin\{
    CardCategoriesController,
    VirtualcardIssueController,
    VirtualcardProviderController,
    VirtualcardFeeslimitsController,
    VirtualcardPreferenceController,
    VirtualcardSpendingControlController,
    VirtualcardController as AdminVirtualcardController,
    VirtualcardTopupController as AdminVirtualcardTopupController,
    VirtualcardHolderController as AdminVirtualcardHolderController,
    VirtualcardWithdrawalController as AdminVirtualcardWithdrawalController
};

use Modules\Virtualcard\Http\Controllers\User\{
    VirtualcardController,
    VirtualcardTopupController,
    VirtualcardHolderController,
    VirtualcardWithdrawalController
};
use Modules\Virtualcard\Http\Controllers\WebhookController;

# Webhook
Route::post('virtualcard/webhook/{provider}', [WebhookController::class, 'handleWebhook'])->name('virtualcard.webhook');

// NOTE:: Need for development Testing
Route::get('virtualcard/webhook/{provider}', [WebhookController::class, 'development'])->name('virtualcard.webhook.development');

# Admin Section
Route::group(config('addons.route_group.authenticated.admin'), function() {

    Route::group(['middleware' => ['addon.status:admin']], function () {

        # Virtualcard Category
        Route::group([
            'controller' => CardCategoriesController::class,
            'as'         => 'admin.card_categories.',
            'prefix'     => 'card/categories'
        ], function () {

            Route::get('/', 'index')->name('index')->middleware(['permission:view_card_category']);
            Route::get('create', 'create')->name('create')->middleware(['permission:add_card_category']);
            Route::post('store', 'store')->name('store')->middleware(['permission:add_card_category']);
            Route::get('{virtualcardCategory}/edit', 'edit')->name('edit')->middleware(['permission:edit_card_category']);
            Route::put('{virtualcardCategory}', 'update')->name('update')->middleware(['permission:edit_card_category']);
            Route::get('destroy/{virtualcardCategory}', 'destroy')->name('destroy')->middleware(['permission:delete_card_category']);
        });


        #Virtualcard preference - Admin
        Route::group([
            'controller' => VirtualcardPreferenceController::class,
            'as'         => 'admin.virtualcard_preference.',
            'prefix'     => 'virtualcard'
        ], function () {

            Route::get('preferences/create', 'create')->name('create')->middleware(['permission:view_card_preference']);
            Route::post('preferences/update', 'update')->name('update')->middleware(['permission:view_card_provider']);
        });

        # Virtualcard Fees Limit - Admin
        Route::group([
            'controller' => VirtualcardFeeslimitsController::class,
            'as' => 'admin.card_fees.',
            'prefix' => 'card/fees-limits'
        ], function () {

            Route::get('/', 'index')->name('index')->middleware(['permission:view_card_fees_limit']);
            Route::get('create', 'create')->name('create')->middleware(['permission:add_card_fees_limit']);
            Route::post('store', 'store')->name('store')->middleware(['permission:add_card_fees_limit']);
            Route::get('{virtualcardFeeslimit}/edit', 'edit')->name('edit')->middleware(['permission:edit_card_fees_limit']);
            Route::put('{virtualcardFeeslimit}', 'update')->name('update')->middleware(['permission:edit_card_fees_limit']);
            Route::get('destroy/{virtualcardFeeslimit}', 'destroy')->name('destroy')->middleware(['permission:delete_card_fees_limit']);
            Route::get('provider/{id}/currencies', 'getProviderCurrency')->name('provider_currency');
        });

        # Virtualcard provider - Admin
        Route::group([
            'controller' => VirtualcardProviderController::class,
            'as'         => 'admin.virtualcard_provider.',
            'prefix'     => 'virtualcard/providers',
        ], function () {

            Route::get('/', 'index')->name('index')->middleware(['permission:view_card_provider']);
            Route::get('create', 'create')->name('create')->middleware(['permission:add_card_provider']);
            Route::post('store', 'store')->name('store')->middleware(['permission:add_card_provider']);
            Route::get('{virtualcardprovider}/edit', 'edit')->name('edit')->middleware(['permission:edit_card_provider']);
            Route::put('{virtualcardprovider}', 'update')->name('update')->middleware(['permission:edit_card_provider']);
            Route::get('destory/{id}', 'destroy')->name('destroy')->middleware(['permission:delete_card_provider']);
            Route::get('{id}/get-currencies',  'getCurrency');

        });

        # Virtualcard Holders - Admin
        Route::group([
            'controller' => AdminVirtualcardHolderController::class,
            'as'         => 'admin.virtualcard_holder.',
            'prefix'     => 'virtualcard/holders'
        ], function() {

            Route::get('csv', 'csv')->name('csv');
            Route::get('pdf', 'pdf')->name('pdf');
            Route::get('user-search', 'cardUserSearch')->name('cardUserSearch');
            Route::get('/', 'index')->name('index')->middleware(['permission:view_card_holder']);
            Route::get('{id}', 'show')->name('show')->middleware(['permission:view_card_holder']);
            Route::get('approve/{virtualcardholder}/{virtualcardprovider}', 'approve')->name('approve')->middleware(['permission:edit_virtual_card']);
            Route::get('reject/{virtualcardholder}', 'reject')->name('reject')->middleware(['permission:edit_virtual_card']);

        });

        # Virtualcard - Admin
        Route::group([
            'controller' => AdminVirtualcardController::class,
            'as' => 'admin.virtualcard.',
            'prefix' => 'virtualcards',
        ], function() {

            Route::get('csv', 'virtualcardCsv');
            Route::get('pdf', 'virtualcardPdf');

            Route::get('/', 'index')->name('index')->middleware(['permission:view_virtual_card']);
            Route::get('{virtualcard}', 'show')->name('show')->middleware(['permission:view_virtual_card']);
            Route::get('{virtualcard}/edit', 'edit')->name('edit')->middleware(['permission:edit_virtual_card']);
            Route::post('{virtualcard}', 'update')->name('update')->middleware(['permission:edit_virtual_card']);

            Route::post('status/update', 'action')->name('action');

        });

        # Virtualcard Spending Control- Admin
        Route::group([
            'controller' => VirtualcardSpendingControlController::class,
            'as' => 'admin.virtualcard_spendingcontrol.',
            'prefix' => 'virtualcards/spending-control',
        ], function() {

            Route::get('limits', 'index')->name('limit');
            Route::post('limits', 'upsert')->name('limit');
            Route::post('limit-exist', 'limitExist')->name('limit_exist');

        });

        # VirtualcardIssue
        Route::group([
            'controller' => VirtualcardIssueController::class,
            'as' => 'admin.virtualcard_issue.',
            'prefix' => 'virtualcard/issue',
        ], function () {

            Route::get('approve/{virtualcardholder}/{virtualcardprovider}/{virtualcard}', 'approve')->name('approve');

            Route::get('manual/{virtualcardholder}/{virtualcardprovider}/{virtualcard}', 'create')->name('create')->middleware(['permission:edit_virtual_card']);

            Route::post('manual/{virtualcardholder}/{virtualcardprovider}/{virtualcard}', 'issue')->name('store')->middleware(['permission:edit_virtual_card']);


            Route::get('reject/{virtualcard}', 'decline')->name('decline')->middleware(['permission:edit_virtual_card']);
        });

        # Virtualcard Topup
        Route::group([
            'controller' => AdminVirtualcardTopupController::class,
            'as'         => 'admin.virtualcard_topup.',
            'prefix'     => 'virtualcard/topups'
        ], function() {

            Route::get('csv', 'csv')->name('csv');
            Route::get('pdf', 'pdf')->name('pdf');

            Route::get('user-search', 'topupUserSearch')->name('topupUserSearch');
            Route::get('/', 'index')->name('index')->middleware(['permission:view_card_topup']);
            Route::get('{id}', 'show')->name('show')->middleware(['permission:view_card_topup']);
            Route::post('change-status/{virtualcardTopup}', 'statusChange')->name('statusChange')->middleware(['permission:edit_card_topup']);
        });

        # Virtualcard Withdrawal
        Route::group([
            'controller' => AdminVirtualcardWithdrawalController::class,
            'as' => 'admin.virtualcard_withdrawal.',
            'prefix' => 'virtualcard/withdrawals'
        ], function () {

            Route::get('/', 'index')->name('index')->middleware(['permission:view_card_withdrawal']);
            Route::get('{virtualcardwithdrawal}/edit', 'edit')->name('edit')->middleware(['permission:view_card_withdrawal']);
            Route::patch('{virtualcardwithdrawal}', 'update')->name('update')->middleware(['permission:edit_card_withdrawal']);

            Route::get('csv', 'csv')->name('csv');
            Route::get('pdf', 'pdf')->name('pdf');
        });

    });

});

# User Section
Route::group(config('addons.route_group.authenticated.user'), function () {

    Route::group(['prefix' => 'virtualcard', 'middleware' => ['card.creator', 'addon.status:users']], function () {

        # Virtualcard Holders - Users
        Route::group([
            'controller' => VirtualcardHolderController::class,
            'as'         => 'user.virtualcard_holder.',
            'middleware' => ['permission:manage_card_holder']
        ], function() {
            Route::get('holders', 'index')->name('index');
            Route::get('holders/create', 'create')->name('create')->middleware('check.kyc');
            Route::post('holders', 'store')->name('store')->middleware('check.kyc');
            Route::get('holders/{virtualcardholder}/edit', 'edit')->name('edit')->middleware('check.kyc');
            Route::post('holders/{virtualcardholder}', 'update')->name('update')->middleware('check.kyc');
            Route::get('holders/duplicate-phone-number-check', 'duplicatePhoneNumberCheck')->name('duplicatePhoneNumberCheck');

        });


        # Virtualcards - User
        Route::group([
            'controller' => VirtualcardController::class,
            'as'         => 'user.virtualcard.',
            'middleware' => ['permission:manage_virtual_card']
        ], function() {

            Route::get('cards/create', 'create')->name('create');
            Route::get('cards', 'index')->name('index');
            Route::post('cards/store', 'store')->name('store');
            Route::get('cards/{virtualcard}', 'show')->name('show');
            Route::post('cards/send-otp', 'sendOtp')->name('send_otp');
            Route::post('cards/verify-otp', 'verifyOtp')->name('verify_otp');
            Route::post('cards/creation-fee', 'creationFee')->name('creation_fee');

        });


        # User Withdrawal
        Route::group([
            'controller' => VirtualcardWithdrawalController::class,
            'as'         => 'user.virtualcard_withdrawal.',
            'prefix'     => 'withdrawals',
            'middleware' => ['permission:manage_card_withdrawal']
        ], function() {

            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create')->middleware('check.kyc');
            Route::post('confirm', 'confirm')->name('confirm')->middleware('check.kyc');
            Route::post('success', 'success')->name('success')->middleware('check.kyc');

            Route::get('pdf/{virtualcardwithdrawal}', 'pdf')->name('pdf');
            Route::get('wallets', 'wallet')->name('wallet');
            Route::post('retrieve-fees-limit', 'retrieveFeesLimit')->name('retrieve_fees_limit');
            Route::post('validate-amount-limit', 'validateAmountLimit')->name('validate_amount_limit');
        });

        # Topup User
        Route::group([
            'controller' => VirtualcardTopupController::class,
            'as'         => 'user.topup.',
            'prefix'     => 'topups',
            'middleware' => ['permission:manage_card_topup']
        ], function() {

            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create')->middleware('check.kyc');
            Route::post('wallets', 'getTopupWaallets')->name('wallets');
            Route::post('feesLimits', 'getTopUpFeesLimit')->name('fees_limit');
            Route::post('confirm', 'topupConfirm')->name('confirm')->middleware('check.kyc');
            Route::post('success', 'topupSuccess')->name('success')->middleware('check.kyc');
            Route::get('print/{id}', 'topupPrintPdf')->name('print');

        });
    });
});
