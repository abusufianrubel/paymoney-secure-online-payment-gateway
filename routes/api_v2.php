<?php

use App\Http\Controllers\Api\V2\QrCodeController;
use App\Http\Controllers\Api\V2\ExchangeMoneyController;
use App\Http\Controllers\Api\V2\RequestMoneyController;
use App\Http\Controllers\Api\V2\AcceptCancelRequestMoneyController;
use App\Http\Controllers\Api\V2\ProfileController;
use App\Http\Controllers\Api\V2\CountryController;
use App\Http\Controllers\Api\V2\DepositMoneyController;
use App\Http\Controllers\Api\V2\ForgotPasswordController;
use App\Http\Controllers\Api\V2\LoginController;
use App\Http\Controllers\Api\V2\PreferenceController;
use App\Http\Controllers\Api\V2\RegistrationController;
use App\Http\Controllers\Api\V2\SendMoneyController;
use App\Http\Controllers\Api\V2\TransactionController;
use App\Http\Controllers\Api\V2\WithdrawalController;
use App\Http\Controllers\Api\V2\WithdrawalSettingController;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('login', [LoginController::class, 'login']);
Route::post('registration', [RegistrationController::class, 'registration']);
Route::post('duplicate-email-check', [RegistrationController::class, 'checkDuplicateEmail']);
Route::post('duplicate-phone-number-check', [RegistrationController::class, 'checkDuplicatePhoneNumber']);
Route::get('default-country-short-name', [CountryController::class, 'getDefaultCountryShortName']);
Route::get('countries', [CountryController::class, 'list']);
Route::get('user-types', [PreferenceController::class, 'userRoles']);

Route::post('forget-password', [ForgotPasswordController::class, 'forgetPassword']);
Route::post('forget-password/verify', [ForgotPasswordController::class, 'verifyResetCode']);
Route::post('forget-password/store', [ForgotPasswordController::class, 'confirmNewPassword']);

/**
 * Preference routes
 */
Route::group(['prefix' => 'preference'], function () {
    Route::get('/', [PreferenceController::class, 'preferenceSettings']);
    Route::get('custom', [PreferenceController::class, 'customSetting']);
    Route::get('check-login-via', [PreferenceController::class, 'checkLoginVia']);
    Route::get('check-processed-by', [PreferenceController::class, 'checkProcessedByApi']);
});

Route::group(['middleware' => ['auth:api-v2', 'check-user-inactive']], function () {
    Route::get('check-user-status', [ProfileController::class, 'checkUserStatus']);
    /**
     * Profile routes
     */
    Route::group(['middleware' => ['permission:manage_setting']], function ()
    {
        Route::group(['prefix' => 'profile'], function () {
            Route::get('/summary', [ProfileController::class, 'summary']);
            Route::get('/details', [ProfileController::class, 'details']);
            Route::put('/update', [ProfileController::class, 'update']);
            Route::post('/upload-image', [ProfileController::class, 'uploadImage']);
            Route::post('/duplicate-phone-number-check', [ProfileController::class, 'checkDuplicatePhoneNumber']);
        });
        Route::post('/change-password', [ProfileController::class, 'changePassword']);
        Route::get('/default-wallet-balance', [ProfileController::class, 'getDefaultWalletBalance']);
        Route::get('/available-balances', [ProfileController::class, 'getUserAvailableWalletsBalance']);
    });

    /**
     * Transaction routes
     */
    Route::group(['prefix' => 'transaction', 'middleware' => ['permission:manage_transaction']], function () {
        Route::get('activityall', [TransactionController::class, 'list']);
        Route::post('details', [TransactionController::class, 'details']);
    });

    /**
     * Send money routes
     */
    Route::group(['name' => 'send-money.', 'prefix' => 'send-money', 'middleware' => ['permission:manage_transfer', 'check-user-suspended']], function () {
        Route::post('/email-check', [SendMoneyController::class, 'emailValidate'])->name('validate-email');
        Route::post('/phone-check', [SendMoneyController::class, 'phoneValidate'])->name('validate-phone');
        Route::get('/get-currencies', [SendMoneyController::class, 'getCurrencies'])->name('get-currencies');
        Route::post('/check-amount-limit', [SendMoneyController::class, 'amountLimitCheck'])->name('check-amount-limit');
        Route::post('/confirm', [SendMoneyController::class, 'sendMoneyConfirm'])->name('confirm');
    });

    /**
     * Accept Money routes
     */
     Route::group(['prefix' => 'accept-money', 'middleware' => ['permission:manage_request_payment', 'check-user-suspended']], function () {
        Route::get('details', [AcceptCancelRequestMoneyController::class, 'details']);
        Route::post('amount-limit-check', [AcceptCancelRequestMoneyController::class, 'checkAmountLimit']);
    });

    /**
     * Exchange money routes
     */
    Route::group(['prefix' => 'exchange-money', 'middleware' => ['permission:manage_exchange', 'check-user-suspended']], function () {
        Route::get('get-currencies', [ExchangeMoneyController::class, 'getCurrencies']);
        Route::post('amount-limit-check', [ExchangeMoneyController::class, 'exchangeLimitCheck']);
        Route::post('get-wallets-balance', [ExchangeMoneyController::class, 'getExchangeWalletsBalance']);
        Route::post('get-destination-wallets', [ExchangeMoneyController::class, 'getExchangableDestinations']);
        Route::post('get-exchange-rate', [ExchangeMoneyController::class, 'getCurrenciesExchangeRate']);
        Route::post('confirm-details', [ExchangeMoneyController::class, 'reviewExchangeDetails']);
        Route::post('complete', [ExchangeMoneyController::class, 'exchangeMoneyComplete']);
    });


    /**
     * Deposit money rotue
     */
    Route::group(['prefix' => 'deposit-money', 'middleware' => ['permission:manage_deposit', 'check-user-suspended']], function () {
        Route::get('get-currencies', [DepositMoneyController::class, 'getCurrencies']);
        Route::post('amount-limit-check', [DepositMoneyController::class, 'validateDepositData']);
        Route::post('payment-methods', [DepositMoneyController::class, 'getPaymentMethod']);
        Route::post('get-bank-list', [DepositMoneyController::class, 'getBankList']);
        Route::post('get-bank-detail', [DepositMoneyController::class, 'getBankDetails']);
        Route::post('stripe-make-payment', [DepositMoneyController::class, 'stripePaymentInitiate']);
        Route::post('payment-confirm', [DepositMoneyController::class, 'paymentConfirm']);
        Route::post('get-paypal-info', [DepositMoneyController::class, 'getPaypalInfo']);
    });


    // Request Money routes
    Route::group(['prefix' => 'request-money', 'middleware' => ['permission:manage_request_payment', 'check-user-suspended']], function () {
        Route::post('email-check', [RequestMoneyController::class, 'checkEmail']);
        Route::post('phone-check', [RequestMoneyController::class, 'checkPhone']);
        Route::get('currencies', [RequestMoneyController::class, 'getCurrency']);
        Route::post('confirm', [RequestMoneyController::class, 'store']);
        Route::post('accept', [AcceptCancelRequestMoneyController::class, 'store']);
        Route::post('cancel-by-creator', [AcceptCancelRequestMoneyController::class, 'cancelByCreator']);
        Route::post('cancel-by-receiver', [AcceptCancelRequestMoneyController::class, 'cancelByReceiver']);

    });

    /**
     * Withdrawal setting routes
     */
    Route::group(['prefix' => 'withdrawal-setting', 'middleware' => ['permission:manage_withdrawal', 'check-user-suspended']], function () {
        Route::get('/payment-methods', [WithdrawalSettingController::class, 'paymentMethods']);
        Route::get('/crypto-currencies', [WithdrawalSettingController::class, 'cryptoCurrencies']);
    });
    Route::resource('/withdrawal-settings', WithdrawalSettingController::class)->middleware('permission:manage_withdrawal', 'check-user-suspended');

    /**
     * Withdrawal routes
     */
    Route::group(['prefix' => 'withdrawal', 'middleware' => ['permission:manage_withdrawal', 'check-user-suspended']], function () {
        Route::post('get-currencies', [WithdrawalController::class, 'getCurrencies']);
        Route::post('amount-limit-check', [WithdrawalController::class, 'amountLimitCheck']);
        Route::post('confirm', [WithdrawalController::class, 'Confirm']);
    });

    /**
     * QrCode
     */
    Route::group(['prefix' => 'qr-code', 'middleware' => ['check-user-suspended']], function () {
        Route::get('get-qr-code', [QrCodeController::class, 'getQrCode']);
        Route::post('add-update-qr-code', [QrCodeController::class, 'addOrUpdateQrCode']);
        Route::post('send-request-qr-operation', [QrCodeController::class, 'sendRequestQrOperation']);
        Route::post('merchant-qr-operation', [QrCodeController::class, 'merchantQrOperation']);
        Route::post('standard-merchant-payment-review', [QrCodeController::class, 'standardMerchantPaymentReview']);
        Route::post('express-merchant-payment-review', [QrCodeController::class, 'expressMerchantPaymentReview']);
        Route::post('standard-merchant-payment-submit', [QrCodeController::class, 'merchantPaymentSubmit']);
    });

});
