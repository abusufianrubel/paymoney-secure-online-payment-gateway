<?php

use Illuminate\Support\Facades\Route;
use Modules\TatumIo\Http\Controllers\TatumIoNotificationController;
use Modules\TatumIo\Http\Controllers\Admin\TatumIoAssetSettingController;
use Modules\TatumIo\Http\Controllers\Admin\CryptoSendReceiveController;
use Modules\TatumIo\Http\Controllers\Users\CryptoReceiveController;
use Modules\TatumIo\Http\Controllers\Users\CryptoSendController;
use Modules\TatumIo\Http\Controllers\Users\TokenSendController;
use Modules\TatumIo\Http\Controllers\Admin\TatumIoTokenController;
use Modules\TatumIo\Http\Controllers\Admin\TatumIoTokenSendController;

Route::any('receive/tatumio-balance-change-notification', [TatumIoNotificationController::class, 'balanceNotification'])->name('tatumio.balance.notification');

Route::group(config('addons.route_group.authenticated.admin'), function() {

    Route::controller(TatumIoAssetSettingController::class)->group(function () {
        Route::get('tatumio/create', 'create')
            ->name('admin.tatumio_asset.create')
            ->middleware('permission:add_crypto_asset');

        Route::post('tatumio/store', 'store')
            ->name('admin.tatumio_asset.store')
            ->middleware('permission:add_crypto_asset')
            ->middleware('demo_check');

        Route::get('tatumio/edit/{network}', 'edit')
            ->name('admin.tatumio_asset.edit')
            ->middleware('permission:edit_crypto_asset');

        Route::post('tatumio/update/{network}', 'update')
            ->name('admin.tatumio_asset.update')
            ->middleware('permission:edit_crypto_asset')
            ->middleware('demo_check');

        Route::get('tatumio/webhook/{network}', 'webhookList')
            ->name('admin.tatumio_asset.webhooklist');

        Route::get('tatumio/webhook/create/{network}', 'webhookCreate')
            ->name('admin.tatumio_asset.webhookcreat');

        Route::post('tatumio/webhook/store', 'webhookStore')
            ->name('admin.tatumio_asset.webhookstore')
            ->middleware('demo_check');

        Route::get('tatumio/webhook/{network}/{id}', 'webhookRemove')
            ->name('admin.tatumio_asset.webhookremove')
            ->middleware('demo_check');

        Route::post('tatumio/asset-status-change', 'assetStatusChange')
            ->name('admin.tatumio_asset.status_change');

        Route::get('tatumio/validate-address', 'validateAddress')
            ->name('admin.tatumio_asset.validate_address');
    });

    Route::controller(CryptoSendReceiveController::class)->group(function () {

        // Admin Crypto Send
        Route::get('tatumio/crypto-send/initiate/{code}', 'cryptoSentInitiate')
            ->name('admin.tatum.crypto_send.create');

        Route::post('tatumio/crypto-send/confirm', 'eachUserCryptoSentConfirm')
            ->name('admin.tatum.crypto_send.confirm')
            ->middleware('demo_check');

        Route::post('tatumio/crypto-send/success', 'eachUserCryptoSentSuccess')
            ->name('admin.tatum.crypto_send.success')
            ->middleware('demo_check');

        Route::get('tatumio/crypto-send/get-merchant-user-network-address-with-merchant-balance', 'getMerchantUserNetworkAddressWithMerchantBalance')
            ->name('admin.tatum.crypto.address_balance');

        Route::get('tatumio/crypto-send/validate-merchant-address-balance', 'validateMerchantAddressBalanceAgainstAmount')
            ->name('admin.tatum.crypto.validate_balance');

        // Admin Crypto Receive
        Route::get('tatumio/crypto-receive/initiate/{code}', 'cryptoReceiveInitiate')
            ->name('admin.tatum.crypto_receive.create');

        Route::post('tatumio/crypto-receive/confirm', 'eachUserCryptoReceiveConfirm')
            ->name('admin.tatum.crypto_receive.confirm')
            ->middleware('demo_check');

        Route::post('tatumio/crypto-receive/success', 'eachUserCryptoReceiveSuccess')
            ->name('admin.tatum.crypto_receive.success')
            ->middleware('demo_check');

        Route::get('tatumio/crypto-receive/get-user-network-address-balance-with-merchant-address', 'getUserNetworkAddressWithUserBalance')
            ->name('admin.tatum.crypto_receive.network_balance');
        Route::get('tatumio/crypto-receive/validate-user-address-balance', 'validateUserAddressBalanceAgainstAmount')
            ->name('admin.tatum.crypto_receive.validate_balance')
            ->middleware('demo_check');

        // Admin crypto send-receive print pdf
        Route::get('tatumio/crypto/send-receive/print/{id}', 'merchantCryptoSentReceivedTransactionPrintPdf')
            ->name('admin.crypto_send_receive.print');
    });

    Route::controller(TatumIoTokenController::class)->group(function () {
        Route::get('crypto-token', 'index')
            ->name('admin.tatumio.token')
            ->middleware('permission:view_crypto_token');

        Route::get('crypto-token/create', 'create')
            ->name('admin.tatumio.token.create')
            ->middleware('permission:add_crypto_token');

        Route::post('crypto-token/store', 'store')
            ->name('admin.tatumio.token.store')
            ->middleware('permission:add_crypto_token')
            ->middleware('demo_check');

        Route::get('crypto-token/edit/{id}', 'edit')
            ->name('admin.tatumio.token.edit')
            ->middleware('permission:edit_crypto_token');

        Route::post('crypto-token/update/{id}', 'update')
            ->name('admin.tatumio.token.update')
            ->middleware('permission:edit_crypto_token')
            ->middleware('demo_check');

        Route::get('crypto-token/adjust', 'adjust')
            ->name('admin.tatumio.token.adjust')
            ->middleware('permission:edit_crypto_token')
            ->middleware('demo_check');
    });

    Route::controller(TatumIoTokenSendController::class)->group(function () {
        Route::get('crypto-token/send/{network}/{tokenid}', 'tokenSendCreate')
            ->name('admin.tatumio.token.send');

        Route::get('crypto-token/admin-token-balance', 'adminTokenBalance')
            ->name('admin.tatum.token.address_balance');

        Route::post('crypto-token/token-send-confirm', 'tokenSendconfirm')
            ->name('admin.tatum.token_send.confirm')
            ->middleware('demo_check');

        Route::post('crypto-token/token-send-success', 'tokenSendSuccess')
            ->name('admin.tatum.token_send.success')
            ->middleware('demo_check');

        Route::get('tatumio/token-send/validate-admin-balance', 'validateAdminBalanceToSendToken')
            ->name('admin.tatum.token.validate_balance');

        Route::get('crypto-token/receive/{network}/{tokenid}', 'tokenReceiveInitiate')
            ->name('admin.tatumio.token.receive');

        Route::get('crypto-token/user-token-balance', 'userTokenBalance')
            ->name('admin.tatum.token.address_balance_user');

        Route::get('tatumio/token-send/validate-user-balance', 'validateUserBalanceToSendToken')
            ->name('admin.tatum.token.validate_balance_user');

        Route::post('crypto-token/token-receive-confirm', 'tokenReceiveConfirm')
            ->name('admin.tatum.token_receive.confirm')
            ->middleware('demo_check');

        Route::post('crypto-token/token-receive-success', 'tokenReceiveSuccess')
            ->name('admin.tatum.token_receive.success')
            ->middleware('demo_check');

        Route::get('tatumio/token/send-receive/print/{id}', 'printPdf')
            ->name('admin.token_send_receive.print');
    });
});

Route::group(config('addons.route_group.authenticated.user'), function() {
    // Crypto Send

    Route::controller(CryptoSendController::class)
        ->prefix('crypto/send/tatumio')
        ->as('tatumio.user.crypto_send.')
        ->middleware('permission:manage_crypto_send_receive')
        ->group(function () {
            Route::get('validate-address', 'validateCryptoAddress')->name('validate_address');
            Route::get('validate-user-balance', 'validateUserBalanceAgainstAmount')->name('validate_balance');
            Route::post('success', 'sendCryptoSuccess')->name('success')->middleware('demo_check');
            Route::post('confirm', 'sendCryptoConfirm')->name('confirm');
            Route::get('{walletCurrencyCode}/{walletId}', 'sendCryptoCreate')->name('create');
        });

    Route::controller(CryptoReceiveController::class)->group(function () {
        // Crypto Receive
        Route::get('crypto/receive/tatumio/{walletCurrencyCode}/{walletId}', 'receiveCrypto')
            ->name('tatumio.user.crypto_receive.create')
            ->middleware(['permission:manage_crypto_send_receive']);

        // Crypto send receive print pdf
        Route::get('transactions/crypto-sent-received-print/{id}', 'cryptoSentReceivedTransactionPrintPdf')
            ->name('user.crypto_send_receive.print');

        // Token Receive
        Route::get('token/receive/tatumio/{walletCurrencyCode}/{walletId}', 'receiveToken')
            ->name('tatumio.user.token_receive.create')
            ->middleware(['permission:manage_crypto_send_receive']);
    });

    // Token Send + token send/receive print
    Route::controller(TokenSendController::class)->group(function () {
        Route::prefix('token/send/tatumio')->as('tatumio.user.token_send.')->namespace('Users')->middleware('permission:manage_crypto_send_receive')->group(function () {
            Route::get('validate-address', 'validateCryptoAddress')->name('validate_address');
            Route::get('validate-user-balance', 'validateUserBalanceAgainstAmount')->name('validate_balance');
            Route::post('success', 'sendTokenSuccess')->name('success')->middleware('demo_check');
            Route::post('confirm', 'sendTokenConfirm')->name('confirm');
            Route::get('{walletCurrencyCode}/{walletId}', 'sendTokenCreate')->name('create');
        });

        Route::get('token-sent-received-print/{id}', 'printPdf')->name('user.token_send_receive.print');
    });
});
