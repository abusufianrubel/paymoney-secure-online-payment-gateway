<?php

use App\Http\Controllers\Users\MoneyTransferController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\CronController;
use App\Http\Controllers\ExpressMerchantPaymentController;
use App\Http\Controllers\GatewayController;
use App\Http\Controllers\Users\DepositController;
use App\Http\Controllers\Users\WithdrawalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MerchantPaymentController;
use App\Http\Controllers\Users\CustomerController;
use App\Http\Controllers\Users\DisputeController;
use App\Http\Controllers\Users\ExchangeController;
use App\Http\Controllers\Users\MerchantController;
use App\Http\Controllers\Users\RequestPaymentController;
use App\Http\Controllers\Users\TicketController;
use App\Http\Controllers\Users\UserTransactionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('initiate-cron', [CronController::class, 'resetData']);
Route::get('/clear', function () {
    Artisan::call('optimize:clear');
    return redirect('/');
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy_policy');

// changing-language
Route::get('change-lang', [HomeController::class, 'setLocalization']);

//coinPayment IPN
Route::post('coinpayment/check', [DepositController::class, 'coinpaymentCheckStatus']);

// user email check on registration
Route::post('user-registration-check-email', [RegisterController::class, 'checkUserRegistrationEmail']);

// Unauthenticated User
Route::group(['middleware' => ['no_auth:users', 'locale']], function () {
    Route::get('/login', [LoginController::class, 'index'])->name("login");
    Route::post('/authenticate', [LoginController::class, 'authenticate']);
    Route::get('register', [RegisterController::class, 'create']);
    Route::post('register/duplicate-phone-number-check', [RegisterController::class, 'registerDuplicatePhoneNumberCheck']);
    Route::post('register/store-personal-info', [RegisterController::class, 'storePersonalInfo'])->name('register.personal.info');
    Route::post('register/store', [RegisterController::class, 'store'])->name('register.store');
    Route::get('/user/verify/{token}', [RegisterController::class, 'verifyUser']);
    Route::view('forget-password', 'frontend.auth.forgetPassword')->name('user.forget_password');
    Route::post('forget-password', [ForgotPasswordController::class, 'forgetPassword']);
    Route::get('password/resets/{token}', [ForgotPasswordController::class, 'verifyToken']);
    Route::post('confirm-password', [ForgotPasswordController::class, 'confirmNewPassword']);
});

//2fa
Route::group(['middleware' => ['guest:users', 'locale', 'check-user-inactive', 'verification'], 'namespace' => 'Users'], function () {
    Route::get('2fa', [CustomerController::class, 'view2fa']);
    Route::post('2fa-resend', [CustomerController::class, 'resend2fa'])->name('2fa-resend');
    Route::post('2fa/verify', [CustomerController::class, 'verify2fa'])->name('2fa-verify.store');
    Route::get('google2fa', [CustomerController::class, 'viewGoogle2fa'])->name('google2fa');
    Route::post('google2fa/verify', [CustomerController::class, 'verifyGoogle2fa'])->name('2fa-verify.google_authenticator');
    Route::post('google2fa/verifyGoogle2faOtp', [CustomerController::class, 'verifyGoogle2faOtp'])->name('2fa-verify.google_otp');
});

// Authenticated User
Route::group(['middleware' => ['guest:users', 'locale', 'twoFa', 'check-user-inactive', 'verification'], 'namespace' => 'Users'], function () {
    Route::get('dashboard', [CustomerController::class, 'dashboard'])->name('user.dashboard')->withoutMiddleware('verification');
    Route::get('wallet-list', [CustomerController::class, 'getWallets'])->name('user.wallets.index');

    Route::get('/logout', [CustomerController::class, 'logout'])->name('user.logout')->withoutMiddleware(['verification', 'twoFa']);
    Route::get('check-user-status', [CustomerController::class, 'checkUserStatus']);
    Route::get('check-request-creator-suspended-status', [CustomerController::class, 'checkRequestCreatorSuspendedStatus'])->name('user.request_money.creator_suspend');
    Route::get('check-request-creator-inactive-status', [CustomerController::class, 'checkRequestCreatorInactiveStatus'])->name('user.request_money.creator_inactive');
    Route::get('check-processed-by', [CustomerController::class, 'checkProcessedBy'])->name('');

    //Settings
    Route::group(['middleware' => ['permission:manage_setting']], function () {
        Route::get('profile', [CustomerController::class, 'profile'])->name('user.profiles.index');
        Route::get('profile/2fa', [CustomerController::class, 'profileTwoFa'])->name('user.setting.twoFa');
        Route::post('profile/2fa/update', [CustomerController::class, 'UpdateProfileTwoFa']);
        Route::post('profile/2fa/ajaxTwoFa', [CustomerController::class, 'ajaxTwoFa'])->name('user.setting.2fa-verify.create');

        //add or update user's qr-code
        Route::post('profile/qr-code/add-or-update', [CustomerController::class, 'addOrUpdateUserProfileQrCode'])->name('user.profile.qrcode.update');
        Route::get('profile/qr-code-print/{id}/{printQrCode}', [CustomerController::class, 'printUserQrCode'])->name('user.profile.qrcode.print');

        //google2fa
        Route::post('profile/2fa/google2fa', [CustomerController::class, 'google2fa'])->name('user.setting.2fa-verify.google');
        Route::post('profile/2fa/google2fa/complete-google2fa-verification', [CustomerController::class, 'completeGoogle2faVerification'])->name('user.setting.2fa-verify.google_complete');
        Route::post('profile/2fa/google2fa/otp-verify', [CustomerController::class, 'google2faOtpVerification'])->name('user.setting.2fa-verify.google_otp');

        //2fa
        Route::post('profile/2fa/disabledTwoFa', [CustomerController::class, 'disabledTwoFa'])->name('user.setting.2fa-verify.disabled');
        Route::post('profile/2fa/ajaxTwoFaSettingsVerify', [CustomerController::class, 'ajaxTwoFaSettingsVerify'])->name('user.setting.2fa-verify.settings');
        Route::post('profile/2fa/check-phone', [CustomerController::class, 'checkPhoneFor2fa'])->name('user.setting.2fa-verify.phone');
        //

        Route::post('prifile/update_password', [CustomerController::class, 'updateProfilePassword'])->name('user.profile.password.update')->middleware('demo_check');
        Route::view('profile-image-upload', 'user.profile.index');
        Route::post('profile-image-upload', [CustomerController::class, 'profileImage'])->name('user.profile.image_upload');
        Route::post('profile/getVerificationCode', [CustomerController::class, 'generatePhoneVerificationCode']);
        Route::post('profile/complete-phone-verification', [CustomerController::class, 'completePhoneVerification']);
        Route::post('profile/add-phone-number', [CustomerController::class, 'addPhoneNumberViaAjax']);
        Route::post('profile/update-phone-number', [CustomerController::class, 'updatePhoneNumberViaAjax']);
        Route::post('profile/editGetVerificationCode', [CustomerController::class, 'editGeneratePhoneVerificationCode']);
        Route::post('profile/edit-complete-phone-verification', [CustomerController::class, 'editCompletePhoneVerification']);
        Route::post('profile/delete-phone-number', [CustomerController::class, 'deletePhoneNumberViaAjax']);
        Route::post('prifile/update', [CustomerController::class, 'updateProfileInfo'])->name('user.profile.update')->middleware('demo_check');
        Route::post('profile/duplicate-phone-number-check', [CustomerController::class, 'userDuplicatePhoneNumberCheck'])->name('user.profile.duplicate_check.phone');
        Route::post('profile/change-default-currency', [CustomerController::class, 'updateDefaultCurrency'])->name('user.profile.default_currency');
    });

    // Deposit - With Suspend Middleware
    Route::group(['middleware' => ['permission:manage_deposit', 'check-user-suspended']], function () {
        Route::get('deposit', [DepositController::class, 'create'])->name('user.deposit.create');
        Route::post('deposit/confirm', [DepositController::class, 'depositConfirm'])->name('user.deposit.confirm');
        Route::post('deposit/gateway', [DepositController::class, 'depositGateway'])->name('user.deposit.gateway');
        Route::post('deposit/getDepositFeesLimit', [DepositController::class, 'getDepositFeesLimit'])->name('user.deposit.fees_limit');
        Route::post('deposit/payment-methods-list', [DepositController::class, 'getPaymentMethods'])->name('user.deposit.payment_method');
        Route::post('deposit/store', [DepositController::class, 'store'])->name('user.deposit.store');
    });

    // Withdrawal - Without Suspend Middleware
    Route::group(['middleware' => ['permission:manage_withdrawal']], function () {
        Route::get('payouts', [WithdrawalController::class, 'payouts'])->name('user.withdrawal.index');
        Route::get('payout/setting', [WithdrawalController::class, 'payoutSetting'])->name('user.withdrawal.setting');
        Route::get('withdrawal-money/print/{id}', [WithdrawalController::class, 'withdrawalPrintPdf'])->name('user.withdrawal.print');
    });

    // Withdrawal - With Suspend Middleware
    Route::group(['middleware' => ['permission:manage_withdrawal', 'check-user-suspended']], function () {
        Route::post('payout/setting/store', [WithdrawalController::class, 'payoutSettingStore'])->name('user.withdrawal.setting.store');
        Route::post('payout/setting/update', [WithdrawalController::class, 'payoutSettingUpdate'])->name('user.withdrawal.setting.update');
        Route::post('payout/setting/delete', [WithdrawalController::class, 'payoutSettingDestroy'])->name('user.withdrawal.setting.delete');

        Route::get('payout', [WithdrawalController::class, 'withdrawalCreate'])->name('user.withdrawal.create');
        Route::post('payout/store', [WithdrawalController::class, 'withdrawalConfirm'])->name('user.withdrawal.confirm');
        Route::get('withdrawal/confirm-transaction', [WithdrawalController::class, 'withdrawalSuccess'])->name('user.withdrawal.success');

        Route::get('withdrawal/method/{id}', [WithdrawalController::class, 'selectWithdrawalMethod']);
        Route::post('withdrawal/store', [WithdrawalController::class, 'withdrawalStore']);
        Route::post('withdrawal/amount-limit', [WithdrawalController::class, 'withdrawalAmountLimitCheck'])->name('user.withdrawal.amount_limit_check');
        Route::post('withdrawal/fees-limit-payment-method-isActive-currencies', [WithdrawalController::class, 'getWithdrawalFeesLimitsActiveCurrencies'])->name('user.withdrawal.active_currencies');
    });

    //Transfer - Without Suspend Middleware
    Route::group(['middleware' => ['permission:manage_transfer']], function () {
        Route::get('moneytransfer/print/{id}', [MoneyTransferController::class, 'transferPrintPdf'])->name('user.send_money.print');
    });

    //Transfer - With Suspend Middleware
    Route::group(['middleware' => ['permission:manage_transfer', 'check-user-suspended']], function () {
        Route::get('moneytransfer', [MoneyTransferController::class, 'create'])->name('user.send_money.create');
        Route::post('transfer', [MoneyTransferController::class, 'store'])->name('user.send_money.store');
        Route::post('transfer-user-email-phone-receiver-status-validate', [MoneyTransferController::class, 'transferUserEmailPhoneReceiverStatusValidate'])->name('user.send_money.receiver_status_check');
        Route::post('amount-limit', [MoneyTransferController::class, 'amountLimitCheck'])->name('user.send_money.check_amount_limit');
        Route::post('send-money-confirm', [MoneyTransferController::class, 'sendMoneyConfirm'])->name('user.send_money.confirm');
    });

    // transactions
    Route::group(['middleware' => ['permission:manage_transaction']], function () {
        Route::get('transactions', [UserTransactionController::class, 'index'])->name('user.transactions.index');
        Route::get('transactions/{id}', [UserTransactionController::class, 'showDetails']);
        Route::post('get_transaction', [UserTransactionController::class, 'getTransaction']);
        Route::get('transactions/print/{id}', [UserTransactionController::class, 'getTransactionPrintPdf'])->name('user.transactions.print');
        Route::get('transactions/exchangeTransactionPrintPdf/{id}', [UserTransactionController::class, 'exchangeTransactionPrintPdf'])->name('user.exchange_transaction.print');
        Route::get('transactions/merchant-payment-print/{id}', [UserTransactionController::class, 'merchantPaymentTransactionPrintPdf'])->name('user.merchant_payment.print');
    });

    // Currency Exchange - Without Suspend Middleware
    Route::group(['middleware' => ['permission:manage_exchange']], function () {
        Route::get('exchange-money/print/{id}', [ExchangeController::class, 'exchangeOfPrintPdf'])->name('user.exchange_money.print');
    });

    // Currency Exchange - With Suspend Middleware
    Route::group(['middleware' => ['permission:manage_exchange', 'check-user-suspended']], function () {
        Route::get('exchange', [ExchangeController::class, 'exchange'])->name('user.exchange_money.create');
        Route::post('exchange-of-money', [ExchangeController::class, 'exchangeOfCurrency'])->name('user.exchange_money.store');
        Route::post('exchange-of-money-success', [ExchangeController::class, 'exchangeOfCurrencyConfirm'])->name('user.exchange_money.confirm');
        Route::post('exchange/get-converted-currencies', [ExchangeController::class, 'getActiveHasTransactionExceptUsersExistingWalletsCurrencies'])->name('user.exchange_money.currency_list_except_selected');
        Route::post('exchange/get-currencies-exchange-rate', [ExchangeController::class, 'getCurrenciesExchangeRate'])->name('user.exchange_money.exchange_rate');
        Route::post('exchange/selected-currency-wallet-balance', [ExchangeController::class, 'getBalanceOfToWallet'])->name('user.exchange_money.wallet_balance');
        Route::post('exchange/amount-limit-check', [ExchangeController::class, 'amountLimitCheck'])->name('user.exchange_money.amount_limit_check');
    });

    // Request Payments - Without Suspend Middleware
    Route::group(['middleware' => ['permission:manage_request_payment']], function () {
        Route::get('request-payment/print/{id}', [RequestPaymentController::class, 'printPdf'])->name('user.request_money.print');
        Route::post('request_payment/cancel', [RequestPaymentController::class, 'cancel'])->name('user.request_money.cancel');
    });

    // Request Payments - With Suspend Middleware
    Route::group(['middleware' => ['permission:manage_request_payment', 'check-user-suspended']], function () {
        Route::get('request_payment/check-creator-status', [RequestPaymentController::class, 'checkReqCreatorStatus'])->name('user.request_money.creator_status_check');
        Route::get('request_payment/add', [RequestPaymentController::class, 'add'])->name('user.request_money.create');
        Route::post('request', [RequestPaymentController::class, 'store'])->name('user.request_money.store');
        Route::get('request_payment/accept/{id}', [RequestPaymentController::class, 'requestAccept'])->name('user.request_money.accept.create');
        Route::post('request-payment/amount-limit', [RequestPaymentController::class, 'amountLimitCheck'])->name('user.request_money.accept.limit');
        Route::post('request_payment/request-user-email-phone-receiver-status-validate', [RequestPaymentController::class, 'requestUserEmailPhoneReceiverStatusValidate'])->name('user.request_money.check_email_or_phone');
        Route::post('request_payment/accepted', [RequestPaymentController::class, 'requestAccepted'])->name('user.request_money.accept.confirm');
        Route::post('request_payment/accept-money-confirm', [RequestPaymentController::class, 'requestAcceptedConfirm'])->name('user.request_money.accept.success');
        Route::post('request-money-confirm', [RequestPaymentController::class, 'requestMoneyConfirm'])->name('user.request_money.confirm');
    });

    // Merchants
    Route::group(['middleware' => ['permission:manage_merchant']], function () {
        Route::get('merchants', [MerchantController::class, 'index'])->name('user.merchants.index');
        Route::get('merchant/detail/{id}', [MerchantController::class, 'detail'])->name('user.merchants.details');
        Route::get('merchant/payments', [MerchantController::class, 'payments'])->name('user.merchants.payments');
        Route::get('merchant/add', [MerchantController::class, 'add'])->name('user.merchants.create');
        Route::post('merchant/store', [MerchantController::class, 'store'])->name('user.merchants.store');
        Route::get('merchant/edit/{id}', [MerchantController::class, 'edit'])->name('user.merchants.edit');
        Route::post('merchant/update', [MerchantController::class, 'update'])->name('user.merchants.update');

        // QR Code - starts
        Route::post('merchant/generate-or-update-standard-merchant-payment-qrcode', [MerchantController::class, 'generateStandardMerchantPaymentQrCode'])->name('user.standard_merchant.payment_qrcode');
        Route::get('merchant/get-express-merchant-qr-code', [MerchantController::class, 'getExpressMerchantQrCode'])->name('user.express_merchant.get_qrcode');
        Route::post('merchant/generate-or-update-express-merchant-qr-code', [MerchantController::class, 'generateOrUpdateExpressMerchantQrCode'])->name('user.express_merchant.qrcode');
        Route::get('merchant/qr-code-print/{id}/{printQrCode}', [MerchantController::class, 'printMerchantQrCode']);
    });

    // Disputes
    Route::group(['middleware' => ['permission:manage_dispute']], function () {
        Route::get('disputes', [DisputeController::class, 'index'])->name('user.disputes.index');
        Route::get('dispute/add/{id}', [DisputeController::class, 'add'])->name('user.disputes.create');
        Route::post('dispute/open', [DisputeController::class, 'store'])->name('user.disputes.store');
        Route::get('dispute/discussion/{id}', [DisputeController::class, 'discussion'])->name('user.disputes.discussion');
        Route::post('dispute/reply', [DisputeController::class, 'storeReply'])->name('user.disputes.reply.store');
        Route::post('dispute/change_reply_status', [DisputeController::class, 'changeReplyStatus'])->name('user.disputes.change_status');
        Route::get('dispute/download/{file}', [DisputeController::class, 'download'])->name('user.disputes.download');
    });

    // Tickets
    Route::group(['middleware' => ['permission:manage_ticket']], function () {
        Route::get('tickets', [TicketController::class, 'index'])->name('user.tickets.index');
        Route::get('ticket/add', [TicketController::class, 'create'])->name('user.tickets.create');
        Route::post('ticket/store', [TicketController::class, 'store'])->name('user.tickets.store');
        Route::get('ticket/reply/{id}', [TicketController::class, 'reply'])->name('user.tickets.reply');
        Route::post('ticket/reply_store', [TicketController::class, 'reply_store'])->name('user.tickets.reply.store');
        Route::post('ticket/change_reply_status', [TicketController::class, 'changeReplyStatus'])->name('user.tickets.change_status');
        Route::get('ticket/download/{file}', [TicketController::class, 'download'])->name('user.tickets.download');
    });
});

/* Merchant Payment Start*/
Route::match(array('GET', 'POST'), 'payment/form', [MerchantPaymentController::class, 'index'])->name('user.merchant.payment_form');
Route::get('payment/method-form', [MerchantPaymentController::class, 'showPaymentForm'])->name('user.merchant.show_payment_form');
Route::get('payment/success', [MerchantPaymentController::class, 'success'])->name('merchant.payment.success');
Route::get('payment/fail', [MerchantPaymentController::class, 'fail']);

//paymoney
Route::post('payment/mts_pay', [MerchantPaymentController::class, 'mtsPayment']);

//stripe
Route::post('payment/stripe', [MerchantPaymentController::class, 'stripePayment']);
Route::post('standard-merchant/stripe-make-payment', [MerchantPaymentController::class, 'stripeMakePayment']);

//paypal
Route::POST('payment/paypal_payment_success', [MerchantPaymentController::class, 'paypalPaymentSuccess']);

//payumoney
Route::post('payment/payumoney', [MerchantPaymentController::class, 'payumoney']);
Route::post('payment/payumoney_success', [MerchantPaymentController::class, 'payuPaymentSuccess']);
Route::post('payment/payumoney_fail', [MerchantPaymentController::class, 'merchantPayumoneyPaymentFail']);

//CoinPayments
Route::post('payment/coinpayments', [MerchantPaymentController::class, 'coinPayments']);
Route::post('payment/coinpayments/make-transaction', [MerchantPaymentController::class, 'coinPaymentMakeTransaction']);
Route::get('payment/coinpayments/coinpayment-transaction-info', [MerchantPaymentController::class, 'viewCoinpaymentTransactionInfo']);

/* PayMoney Merchant API Start*/
Route::post('merchant/api/verify', [ExpressMerchantPaymentController::class, 'verifyClient']);
Route::match(array('GET', 'POST'), 'merchant/payment', [ExpressMerchantPaymentController::class, 'generatedUrl']);
Route::post('merchant/api/transaction-info', [ExpressMerchantPaymentController::class, 'storeTransactionInfo']);
Route::get('merchant/payment/cancel', [ExpressMerchantPaymentController::class, 'cancelPayment']);
Route::get('merchant/payment/success', [ExpressMerchantPaymentController::class, 'paymentSuccess'])->name('express.payment.succss');
Route::get('merchant/payment/redirect', [ExpressMerchantPaymentController::class, 'redirectSuccessPath'])->name('express.payment.redirect');

Route::group(['middleware' => ['guest:users']], function () {
    Route::get('merchant/payment/confirm', [ExpressMerchantPaymentController::class, 'confirmPayment']);
});


Route::get('gateway-payment/success', [MerchantPaymentController::class, 'paymentSuccess'])->name('gateway.payment.success');

Route::get('gateway/payment/{gateway}', [GatewayController::class, 'pay'])->name('gateway.pay');
Route::post('gateway/confirm-payment', [GatewayController::class, 'confirmPayment'])->name('gateway.confirm_payment');
Route::match(array('GET', 'POST'), 'gateway/payment-verify/{gateway}', [GatewayController::class, 'verify'])->name('gateway.payment_verify');
Route::match(array('GET', 'POST'), 'gateway/payment-cancel/{gateway}', [GatewayController::class, 'cancelPayment'])->name('gateway.payment_cancel');
Route::match(array('GET', 'POST'), 'gateway/process-payment/{gateway}', [GatewayController::class, 'processPayment'])->name('gateway.process_payment');




Route::post('deposit/bank-payment/get-bank-detail', [DepositController::class, 'getBankDetailOnChange'])->name('user.deposit.bank.details');
Route::get('deposit/success', [DepositController::class, 'depositSuccess'])->name('user.deposit.success');
Route::get('deposit/complete', [DepositController::class, 'depositComplete'])->name('deposit.complete');
Route::get('deposit-money/print/{id}', [DepositController::class, 'depositPrintPdf'])->name('user.deposit.print');

Route::get('coinpayment/summery', [DepositController::class, 'coinPaymentSummary'])->name('coinpayment.summery');



Route::get('download/package', [ContentController::class, 'downloadPackage']);
if (count(request()->segments()) <= 1) {
    Route::get('{url}', [ContentController::class, 'pageDetail'])->where('url', urlRouteExcept());
}
