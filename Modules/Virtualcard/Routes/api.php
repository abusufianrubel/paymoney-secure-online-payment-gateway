<?php

use Illuminate\Support\Facades\Route;
use Modules\Virtualcard\Http\Controllers\Api\VirtualcardApiController;
use Modules\Virtualcard\Http\Controllers\Api\VirtualcardHolderApiController;
use Modules\Virtualcard\Http\Controllers\Api\VirtualcardRequestApiController;
use Modules\Virtualcard\Http\Controllers\Api\VirtualcardTopupApiController;
use Modules\Virtualcard\Http\Controllers\Api\VirtualcardTransactionApiController;
use Modules\Virtualcard\Http\Controllers\Api\VirtualcardWithdrawalApiController;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/

Route::group(['namespace' => 'Api', 'middleware' => ['auth:api-v2']], function() {

    # Request New holder
    Route::group([
        'controller' => VirtualcardHolderApiController::class,
        'prefix' => 'virtualcard/holders'
    ], function () {
            Route::get('/', 'index');
            Route::get('{id}', 'details');
            Route::post('store', 'store');
    });

    # Request New Card
    Route::group([
        'controller' => VirtualcardRequestApiController::class,
        'prefix' => 'virtualcards'
    ], function() {
        Route::get('preferred-currencies', 'currency');
        Route::get('preferred-categories', 'category');
        Route::get('active-card-holders', 'cardHolder');
        Route::get('perferred-networks', 'network');
        Route::post('creation-fee', 'creationFee');
        Route::post('request-new-card', 'store');
    });

    # Card Transactions 
    Route::group([
        'controller' => VirtualcardTransactionApiController::class,
        'prefix' => 'virtualcard/transactions'
    ], function () {
        Route::get('{virtualcard_id}', 'index');
        Route::get('{virtualcard_id}/{transaction_id}', 'virtualcardTransactionDetails');
    });

    # Cards
    Route::group([
        'controller' => VirtualcardApiController::class,
        'prefix' => 'virtualcards'
    ], function () {

        Route::get('/', 'index');
        Route::get('{id}', 'show');
        Route::post('send-otp', 'sendOtp');
        Route::post('verify-otp', 'verifyOtp');

    });

    #Topup
    Route::group([
        'controller' => VirtualcardTopupApiController::class,
        'prefix'     => 'virtualcard/topup'
    ], function () {

        Route::post('card', 'virtualcard');
        Route::post('wallets', 'wallets');
        Route::post('feesLimit', 'getTopUpFeesLimit');
        Route::post('confirm', 'topupConfirm');
        Route::post('success', 'topupSuccess');
    });

    #Withdrawal
    Route::group([
        'controller' => VirtualcardWithdrawalApiController::class,
        'prefix'     => 'virtualcard/withdrawal'
    ], function () {

        Route::post('card', 'virtualcard');
        Route::post('wallets', 'wallets');
        Route::post('retrieve-fees-limit', 'retrieveFeesLimit');
        Route::post('validate-amount-limit', 'validateAmountLimit');
        Route::post('confirm', 'confirm');
        Route::post('success', 'success');
    });
});
