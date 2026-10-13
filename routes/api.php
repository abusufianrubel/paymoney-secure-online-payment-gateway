<?php

use App\Http\Controllers\Api\AcceptCancelRequestMoneyController;
use App\Http\Controllers\Api\DepositMoneyController;
use App\Http\Controllers\Api\ExchangeMoneyController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\PayoutMoneyController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\RequestMoneyController;
use App\Http\Controllers\Api\PayoutSettingController;
use App\Http\Controllers\Api\QrCodeController;
use App\Http\Controllers\Api\SendMoneyController;
use Illuminate\Support\Facades\Route;


//Route for Preference Settings
Route::get('get-preference-settings', [LoginController::class, 'getPreferenceSettings']);

//Route for Paymoney Settings
Route::get('get-paymoney-settings', [LoginController::class, 'getPaymoneySettingsFromApi']);

//Check User Inactive Status
Route::get('check-user-status', [ProfileController::class, 'checkUserStatusApi']);

Route::get('check-login-via', [LoginController::class, 'checkLoginVia']);
Route::post('login', [LoginController::class, 'login']);

//Routes for Registration
Route::get('check-merchant-user-role-existence', [RegistrationController::class, 'getMerchantUserRoleExistence']);
Route::post('registration', [RegistrationController::class, 'registration']);
Route::post('registration/duplicate-email-check', [RegistrationController::class, 'duplicateEmailCheckApi']);
Route::post('registration/duplicate-phone-number-check', [RegistrationController::class, 'duplicatePhoneNumberCheckApi']);
Route::get('registration/get-default-country-short-name', [RegistrationController::class, 'getDefaultCountryShortName']);


/*
|--------------------------------------------------------------------------
| API Routes - With Authorization Middleware
|--------------------------------------------------------------------------
 */
Route::group(['middleware' => ['check-authorization-token']], function ()//bug-fixed-pm-v2.7
{
    //Routes for User Profile starts here
    Route::get('get-default-wallet-balance', [ProfileController::class, 'getDefaultWalletBalance']);

    Route::get('get-user-profile', [ProfileController::class, 'getUserProfile']);
    Route::post('update-user-profile', [ProfileController::class, 'updateUserProfile']);
    Route::get('get-user-specific-details', [ProfileController::class, 'getUserSpecificProfile']);
    Route::post('profile/duplicate-email-check', [ProfileController::class, 'userProfileDuplicateEmailCheckApi']);
    Route::get('check-processed-by', [ProfileController::class, 'checkProcessedByApi']);
    Route::post('profile-image-upload', [ProfileController::class, 'profileImage']);
    Route::post('update-password', [ProfileController::class, 'updatePassword']);

    //Route for User Profile Ends here

    //Route for User's Available Balance
    Route::get('available-balance', [ProfileController::class, 'getUserAvailableWalletsBalances']);

    //Routes for Transactions starts here
    Route::get('activityall', [TransactionController::class, 'getTransactionApi']);
    Route::get('transaction-details', [TransactionController::class, 'getTransactionDetailsApi']);
    Route::get('transaction-details/request-payment/check-creator-status', [RequestMoneyController::class, 'checkReqCreatorStatusApi']);
    //Route for Transactions Ends here

    //Routes for deposit Starts here
    Route::get('get-deposit-currency-list', [DepositMoneyController::class, 'getDepositCurrencyList']);
    Route::get('get-deposit-bank-list', [DepositMoneyController::class, 'getDepositBankList']);
    Route::post('fees-limit-currency-payment-methods-is-active-payment-methods-list', [DepositMoneyController::class, 'getDepositMatchedFeesLimitsCurrencyPaymentMethodsSettingsPaymentMethods']);
    Route::get('get-deposit-details-with-amount-limit-check', [DepositMoneyController::class, 'getDepositDetailsWithAmountLimitCheck']);
    Route::post('deposit/get-bank-detail', [DepositMoneyController::class, 'getBankDetails']);
    Route::post('deposit/bank-payment-store', [DepositMoneyController::class, 'bankPaymentStore']);
    Route::post('deposit/coinpayments-payment-store', [DepositMoneyController::class, 'coinpaymentsPaymentStore']);
    Route::post('deposit/stripe-make-payment', [DepositMoneyController::class, 'stripeMakePayment']);
    Route::post('deposit/stripe-confirm-payment', [DepositMoneyController::class, 'stripeConfirm']);
    Route::post('deposit/get-paypal-info', [DepositMoneyController::class, 'getPeypalInfo']);
    Route::post('deposit/paypal-payment-store', [DepositMoneyController::class, 'paypalPaymentStore']);
    //Routes for deposit ends here

    //Routes for withdraw Setting Starts here
    Route::get('payout-setting', [PayoutSettingController::class, 'index']);
    Route::POST('add-withdraw-setting', [PayoutSettingController::class, 'store']);
    Route::POST('edit-withdraw-setting', [PayoutSettingController::class, 'update']);
    Route::POST('delete-payout-setting', [PayoutSettingController::class, 'delete']);
    Route::get('get-withdraw-payment-methods', [PayoutSettingController::class, 'paymentMethods']);
    Route::get('withdrawal/get-all-countries', [PayoutSettingController::class, 'getAllCountries']);
    Route::get('withdrawal/get-withdrawal-crypto-currencies', [PayoutSettingController::class, 'getWithdrawalCryptoCurrencies']);
    //Routes for withdraw Setting ends here

    //Routes for withdraw Starts here
    Route::get('check-payout-settings', [PayoutMoneyController::class, 'checkPayoutSettingsApi']);
    Route::get('get-withdraw-payment-method', [PayoutMoneyController::class, 'getWithdrawalPaymentMethod']);
    Route::get('get-withdraw-currencies-based-on-payment-method', [PayoutMoneyController::class, 'getWithdrawalCurrencyBasedOnPaymentMethod']);
    Route::get('get-withdraw-details-with-amount-limit-check', [PayoutMoneyController::class, 'getWithdrawDetailsWithAmountLimitCheck']);
    Route::post('withdraw-money-pay', [PayoutMoneyController::class, 'withdrawMoneyConfirm']);
    //Routes for withdraw ends here

    //Route for Send Money Starts here
    Route::post('send-money-email-check', [SendMoneyController::class, 'postSendMoneyEmailCheckApi']);
    Route::post('send-money-phone-check', [SendMoneyController::class, 'postSendMoneyPhoneCheckApi']);
    Route::get('get-send-money-currencies', [SendMoneyController::class, 'getSendMoneyCurrenciesApi']);
    Route::post('check-send-money-amount-limit', [SendMoneyController::class, 'postSendMoneyFeesAmountLimitCheckApi']);
    Route::post('send-money-pay', [SendMoneyController::class, 'postSendMoneyPayApi']);
    //Routes for Send Money Ends here

    //Routes for Request Money Starts here
    Route::post('request-money-email-check', [RequestMoneyController::class, 'postRequestMoneyEmailCheckApi']);
    Route::post('request-money-phone-check', [RequestMoneyController::class, 'postRequestMoneyPhoneCheckApi']);//
    Route::get('get-request-currency', [RequestMoneyController::class, 'getRequestMoneyCurrenciesApi']);//
    Route::post('request-money-pay', [RequestMoneyController::class, 'postRequestMoneyPayApi']);
    //Routes for Request Money Ends here

    //Routes for accept/cancel request payment starts here
    Route::get('accept-request-email-phone', [AcceptCancelRequestMoneyController::class, 'getAcceptRequestEmailOrPhone']);
    Route::post('request-accept-amount-limit-check', [AcceptCancelRequestMoneyController::class, 'getAcceptRequestAmountLimit']);
    Route::get('get-accept-fees-details', [AcceptCancelRequestMoneyController::class, 'getAcceptFeesDetails']);
    Route::post('accept-request-payment-pay', [AcceptCancelRequestMoneyController::class, 'requestAcceptedConfirm']);
    Route::post('cancel-request', [AcceptCancelRequestMoneyController::class, 'requestCancel']);
    //Routes for accept/cancel request payment ends here

    //Routes for exchange money starts here
    Route::get('get-User-Wallets-WithActive-HasTransaction', [ExchangeMoneyController::class, 'getUserWalletsWithActiveAndHasTransactionCurrency']);
    Route::post('exchange-review', [ExchangeMoneyController::class, 'exchangeReview']);
    Route::post('getBalanceOfFromAndToWallet', [ExchangeMoneyController::class, 'getBalanceOfFromAndToWallet']);
    Route::post('getWalletsExceptSelectedFromWallet', [ExchangeMoneyController::class, 'getWalletsExceptSelectedFromWallet']);
    Route::post('get-currencies-exchange-rate', [ExchangeMoneyController::class, 'getCurrenciesExchangeRate']);
    Route::post('review-exchange-details', [ExchangeMoneyController::class, 'reviewExchangeDetails']);
    Route::post('exchange-money-complete', [ExchangeMoneyController::class, 'exchangeMoneyComplete']);
    //Routes for exchange money ends here

    //Route for Logout
    Route::post('logout', [LoginController::class, 'logout']);

    //qr-code
    Route::post('perform-qr-code-operation', [QrCodeController::class, 'performQrCodeOperationApi']); //new
    Route::get('get-user-qr-code', [QrCodeController::class, 'getUserQrCode']); //new
    Route::post('add-or-update-user-qr-code', [QrCodeController::class, 'addOrUpdateUserQrCode']); //new
 
    //Send Money & Request Money - QrCode Operations
    Route::post('send-money-request-money-perform-qr-code-operation', [QrCodeController::class, 'performSendMoneyRequestMoneyQrCodeOperationApi']); //new
 
    //Standard Merchant - QrCode Operations
    Route::post('perform-merchant-payment-qr-code-review', [QrCodeController::class, 'performMerchantPaymentQrCodeReviewApi']); //new
    Route::post('perform-merchant-payment-qr-code-submit', [QrCodeController::class, 'performMerchantPaymentQrCodeSubmit']); //new
 
    //Express Merchant - QrCode Operations
    Route::post('perform-express-merchant-payment-qr-code-merchant-currency-user-wallets-review', [QrCodeController::class, 'performExpressMerchantPaymentMerchantCurrencyUserWalletsReviewApi']); //new
    Route::post('perform-express-merchant-payment-qr-code-merchant-amount-review', [QrCodeController::class, 'performExpressMerchantPaymentAmountReviewApi']); //new
    Route::post('perform-express-merchant-payment-qr-code-submit', [QrCodeController::class, 'performExpressMerchantPaymentQrCodeSubmit']);
});

// Route::middleware(['auth:api', 'permission:manage_merchant']);//permission test
