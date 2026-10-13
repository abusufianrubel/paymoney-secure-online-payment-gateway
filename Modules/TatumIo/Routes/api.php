<?php

use Illuminate\Support\Facades\Route;
use Modules\TatumIo\Http\Controllers\Api\CryptoSendController;
use Modules\TatumIo\Http\Controllers\Api\TokenSendController;

Route::group(['prefix' => 'crypto/send/tatumio', 'middleware' => ['auth:api-v2', 'check-user-suspended']], function () {

    Route::controller(CryptoSendController::class)->group(function () {
        Route::get('provider-status', 'providerStatus');
        Route::get('validate-address', 'validateCryptoAddress');
        Route::get('user-address/{walletCurrencyCode}', 'userCryptoAddress');
        Route::get('validate-user-balance', 'validateUserBalanceAgainstAmount');
        Route::post('confirm', 'cryptoSendConfirm');
    });
});

Route::group(['prefix' => 'token/send/tatumio', 'middleware' => ['auth:api-v2', 'check-user-suspended']], function () {
    Route::controller(TokenSendController::class)->group(function () {
        Route::get('provider-status', 'providerStatus');
        Route::get('validate-address', 'validateCryptoAddress');
        Route::get('user-address/{walletCurrencyCode}/{walletId}', 'userCryptoAddress');
        Route::get('validate-user-balance', 'validateUserBalanceAgainstAmount');
        Route::post('confirm', 'cryptoSendConfirm');
    });
});

