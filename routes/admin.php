<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AddonController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AppStoreCredentialController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\CryptoProviderController;
use App\Http\Controllers\Admin\CryptoReceivedTransactionController;
use App\Http\Controllers\Admin\CryptoSentTransactionController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\CurrencyPaymentMethodController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepositController;
use App\Http\Controllers\Admin\DisputeController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\ExchangeController;
use App\Http\Controllers\Admin\FeesLimitController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\TokenReceivedTransactionController;
use App\Http\Controllers\Admin\TokenSentTransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MerchantController;
use App\Http\Controllers\Admin\MerchantGroupController;
use App\Http\Controllers\Admin\MerchantPaymentController;
use App\Http\Controllers\Admin\MetaController;
use App\Http\Controllers\Admin\ModuleManagerController;
use App\Http\Controllers\Admin\MoneyTransferController;
use App\Http\Controllers\Admin\NotificationSettingController;
use App\Http\Controllers\Admin\NotificationTypeController;
use App\Http\Controllers\Admin\PagesController;
use App\Http\Controllers\Admin\RequestPaymentController;
use App\Http\Controllers\Admin\RevenueController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SmsTemplateController;
use App\Http\Controllers\Admin\SystemInfoController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UsersRoleController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Helpers\Common;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

// Unauthenticated Admin
Route::group(['middleware' => ['no_auth:admin', 'locale', 'ip_middleware']], function ()
{
    Route::get('/', function () {
        return view('admin.auth.login');
    })->name('admin');

    Route::post('adminlog', [AdminController::class, 'authenticate']);
    Route::view('forget-password', 'admin.auth.forgetPassword');
    Route::post('forget-password', [AdminController::class, 'forgetPassword']);
    Route::get('password/resets/{token}', [AdminController::class, 'verifyToken']);
    Route::post('confirm-password', [AdminController::class, 'confirmNewPassword']);
});

// Authenticated Admin
Route::group(['middleware' => ['guest:admin', 'locale', 'ip_middleware']], function ()
{
    Route::get('/clear-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        (new Common())->one_time_message('success',__('Cache successfully cleared.'));
        return back();
    })->name('clear.cache');

    Route::get('home', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('adminlogout', [AdminController::class, 'logout']);
    Route::get('profile', [AdminController::class, 'profile']);
    Route::post('update-admin/{id}', [AdminController::class, 'update']);

    Route::get('change-password', [AdminController::class, 'changePassword']);
    Route::post('change-password', [AdminController::class, 'updatePassword'])->middleware('demo_check');

    Route::post('check-password', [AdminController::class, 'passwordCheck']);

    // Change language
    Route::post('change-lang', [DashboardController::class, 'switchLanguage']);

    // users
    Route::get('users', [UserController::class, 'index'])->middleware(['permission:view_user']);
    Route::get('users/create', [UserController::class, 'create'])->middleware(['permission:add_user']);
    Route::post('users/store', [UserController::class, 'store']);
    Route::get('users/view/{id}', [UserController::class, 'show']);
    Route::get('users/edit/{id}', [UserController::class, 'edit'])->middleware(['permission:edit_user']);
    Route::post('users/update', [UserController::class, 'update'])->middleware('demo_check');
    Route::get('users/delete/{id}', [UserController::class, 'destroy'])->middleware(['permission:delete_role', 'demo_check']);

    Route::post('email_check', [UserController::class, 'postEmailCheck']);
    Route::post('duplicate-phone-number-check', [UserController::class, 'duplicatePhoneNumberCheck']);
    Route::get('users/transactions/{id}', [UserController::class, 'eachUserTransaction']);

    //Admin Can deposit for a user
    Route::match(array('GET', 'POST'), 'users/deposit/create/{id}', [UserController::class, 'eachUserDeposit']);
    Route::post('users/deposit/amount-fees-limit-check', [UserController::class, 'amountFeesLimitCheck']);
    Route::post('users/deposit/storeFromAdmin', [UserController::class, 'eachUserDepositSuccess']);
    Route::get('users/deposit/print/{id}', [UserController::class, 'eachUserdepositPrintPdf']);

    //Admin Can withdraw for a user
    Route::match(array('GET', 'POST'), 'users/withdraw/create/{id}', [UserController::class, 'eachUserWithdraw']);
    Route::post('users/withdraw/amount-fees-limit-check', [UserController::class, 'amountFeesLimitCheck']);
    Route::post('users/withdraw/storeFromAdmin', [UserController::class, 'eachUserWithdrawSuccess']);
    Route::get('users/withdraw/print/{id}', [UserController::class, 'eachUserWithdrawPrintPdf']);

    Route::get('users/wallets/{id}', [UserController::class, 'eachUserWallet']);
    Route::get('users/tickets/{id}', [UserController::class, 'eachUserTicket']);
    Route::get('users/disputes/{id}', [UserController::class, 'eachUserDispute']);

    // Crypto Send Transactions details
    Route::get('crypto-sent-transactions', [CryptoSentTransactionController::class, 'index'])->name('admin.crypto_sent_transaction.index')->middleware('permission:view_crypto_transactions');
    Route::get('crypto-sent-transactions/csv', [CryptoSentTransactionController::class, 'cryptoSentTransactionsCsv'])->name('admin.crypto_sent_transaction.csv');
    Route::get('crypto-sent-transactions/pdf', [CryptoSentTransactionController::class, 'cryptoSentTransactionsPdf'])->name('admin.crypto_sent_transaction.pdf');
    Route::get('crypto-sent-transactions/view/{id}', [CryptoSentTransactionController::class, 'view'])->name('admin.crypto_sent_transaction.view')->middleware('permission:view_crypto_transactions');
    Route::get('crypto-sent-transactions/search/user', [CryptoSentTransactionController::class, 'cryptoSentTransactionsSearchUser'])->name('admin.crypto_sent_transaction.search_user');

    // Crypto Receive Transactions
    Route::get('crypto-received-transactions', [CryptoReceivedTransactionController::class, 'index'])->name('admin.crypto_received_transaction.index')->middleware('permission:view_crypto_transactions');
    Route::get('crypto-received-transactions/csv', [CryptoReceivedTransactionController::class, 'cryptoReceivedTransactionsCsv'])->name('admin.crypto_received_transaction.csv');
    Route::get('crypto-received-transactions/pdf', [CryptoReceivedTransactionController::class, 'cryptoReceivedTransactionsPdf'])->name('admin.crypto_received_transaction.pdf');
    Route::get('crypto-received-transactions/view/{id}', [CryptoReceivedTransactionController::class, 'view'])->name('admin.crypto_received_transaction.view')->middleware('permission:view_crypto_transactions');
    Route::get('crypto-received-transactions/search/user', [CryptoReceivedTransactionController::class, 'cryptoReceivedTransactionsSearchUser'])->name('admin.crypto_received_transaction.search_user');


    // Token Send Transactions details
    Route::get('token-sent-transactions', [TokenSentTransactionController::class, 'index'])->name('admin.token_sent_transaction.index')->middleware('permission:view_token_transactions');
    Route::get('token-sent-transactions/csv', [TokenSentTransactionController::class, 'tokenSentTransactionsCsv'])->name('admin.token_sent_transaction.csv');
    Route::get('token-sent-transactions/pdf', [TokenSentTransactionController::class, 'tokenSentTransactionsPdf'])->name('admin.token_sent_transaction.pdf');
    Route::get('token-sent-transactions/view/{id}', [TokenSentTransactionController::class, 'view'])->name('admin.token_sent_transaction.view')->middleware('permission:view_token_transactions');
    Route::get('token-sent-transactions/search/user', [TokenSentTransactionController::class, 'tokenSentTransactionsSearchUser'])->name('admin.token_sent_transaction.search_user');

    // Token Receive Transactions
    Route::get('token-received-transactions', [TokenReceivedTransactionController::class, 'index'])->name('admin.token_received_transaction.index')->middleware('permission:view_token_transactions');
    Route::get('token-received-transactions/csv', [TokenReceivedTransactionController::class, 'tokenReceivedTransactionsCsv'])->name('admin.token_received_transaction.csv');
    Route::get('token-received-transactions/pdf', [TokenReceivedTransactionController::class, 'tokenReceivedTransactionsPdf'])->name('admin.token_received_transaction.pdf');
    Route::get('token-received-transactions/view/{id}', [TokenReceivedTransactionController::class, 'view'])->name('admin.token_received_transaction.view')->middleware('permission:view_token_transactions');
    Route::get('token-received-transactions/search/user', [TokenReceivedTransactionController::class, 'tokenReceivedTransactionsSearchUser'])->name('admin.token_received_transaction.search_user');


    // admin_users
    Route::get('admin_users', [UserController::class, 'adminList'])->middleware(['permission:view_admins']);
    Route::get('admin-user/create', [UserController::class, 'adminCreate'])->middleware(['permission:add_admin']);
    Route::post('admin-users/store', [UserController::class, 'adminStore']);
    Route::get('admin-user/edit/{id}', [UserController::class, 'adminEdit'])->middleware(['permission:edit_admin']);
    Route::post('admin-users/update', [UserController::class, 'adminUpdate'])->middleware('demo_check');
    Route::get('admin-user/delete/{id}', [UserController::class, 'adminDestroy'])->middleware(['permission:delete_admin', 'demo_check']);

    // Merchants
    Route::get('merchants', [MerchantController::class, 'index'])->middleware(['permission:view_merchant']);
    Route::get('merchant/edit/{id}', [MerchantController::class, 'edit'])->middleware(['permission:edit_merchant']);
    Route::post('merchant/update', [MerchantController::class, 'update']);
    Route::post('merchant/logo_delete', [MerchantController::class, 'deleteLogo']);
    Route::post('merchant/delete-merchant-logo', [MerchantController::class, 'deleteMerchantLogo']);
    Route::get('merchant/payments/{id}', [MerchantController::class, 'eachMerchantPayment']);
    Route::get('merchants/userSearch', [MerchantController::class, 'merchantsUserSearch']);
    Route::get('merchants/csv', [MerchantController::class, 'merchantCsv']);
    Route::get('merchants/pdf', [MerchantController::class, 'merchantPdf']);
    Route::post('merchants/change-fee-with-group-change', [MerchantController::class, 'changeMerchantFeeWithGroupChange']);

    //Merchant Payments
    Route::get('merchant_payments', [MerchantPaymentController::class, 'index'])->middleware(['permission:view_merchant_payment']);
    Route::get('merchant_payments/edit/{id}', [MerchantPaymentController::class, 'edit'])->middleware(['permission:edit_merchant_payment']);
    Route::post('merchant_payments/update', [MerchantPaymentController::class, 'update']);
    Route::get('merchant_payments/csv', [MerchantPaymentController::class, 'merchantPaymentCsv']);
    Route::get('merchant_payments/pdf', [MerchantPaymentController::class, 'merchantPaymentPdf']);

    // Transactions
    Route::get('transactions', [TransactionController::class, 'index'])->middleware(['permission:view_transaction']);
    Route::get('transactions/edit/{id}', [TransactionController::class, 'edit'])->middleware(['permission:edit_transaction']);
    Route::post('transactions/update/{id}', [TransactionController::class, 'update']);
    Route::get('transactions_user_search', [TransactionController::class, 'transactionsUserSearch']);
    Route::get('transactions/csv', [TransactionController::class, 'transactionCsv']);
    Route::get('transactions/pdf', [TransactionController::class, 'transactionPdf']);

    // Deposits
    Route::get('deposits', [DepositController::class, 'index'])->middleware(['permission:view_deposit']);
    Route::get('deposits/edit/{id}', [DepositController::class, 'edit'])->middleware(['permission:edit_deposit']);
    Route::post('deposits/update', [DepositController::class, 'update']);
    Route::get('deposits/user_search', [DepositController::class, 'depositsUserSearch']);
    Route::get('deposits/csv', [DepositController::class, 'depositCsv']);
    Route::get('deposits/pdf', [DepositController::class, 'depositPdf']);

    // Withdrawals
    Route::get('withdrawals', [WithdrawalController::class, 'index'])->middleware(['permission:view_withdrawal']);
    Route::get('withdrawals/edit/{id}', [WithdrawalController::class, 'edit'])->middleware(['permission:edit_withdrawal']);
    Route::post('withdrawals/update', [WithdrawalController::class, 'update']);
    Route::get('withdrawals/user_search', [WithdrawalController::class, 'withdrawalsUserSearch']);
    Route::get('withdrawals/csv', [WithdrawalController::class, 'withdrawalCsv']);
    Route::get('withdrawals/pdf', [WithdrawalController::class, 'withdrawalPdf']);

    // Transfers
    Route::get('transfers', [MoneyTransferController::class, 'index'])->middleware(['permission:view_transfer']);
    Route::get('transfers/edit/{id}', [MoneyTransferController::class, 'edit'])->middleware(['permission:edit_transfer']);
    Route::post('transfers/update', [MoneyTransferController::class, 'update']);
    Route::get('transfers/user_search', [MoneyTransferController::class, 'transfersUserSearch']);
    Route::get('transfers/csv', [MoneyTransferController::class, 'transferCsv']);
    Route::get('transfers/pdf', [MoneyTransferController::class, 'transferPdf']);

    // Currency Exchanges
    Route::get('exchanges', [ExchangeController::class, 'index'])->middleware(['permission:view_exchange']);
    Route::get('exchange/edit/{id}', [ExchangeController::class, 'edit'])->middleware(['permission:edit_exchange']);
    Route::post('exchange/update', [ExchangeController::class, 'update']);
    Route::get('exchanges/user_search', [ExchangeController::class, 'exchangesUserSearch']);
    Route::get('exchanges/csv', [ExchangeController::class, 'exchangeCsv']);
    Route::get('exchanges/pdf', [ExchangeController::class, 'exchangePdf']);

    // Request Payments
    Route::get('request_payments', [RequestPaymentController::class, 'index'])->middleware(['permission:view_request_payment']);
    Route::get('request_payments/edit/{id}', [RequestPaymentController::class, 'edit'])->middleware(['permission:edit_request_payment']);
    Route::post('request_payments/update', [RequestPaymentController::class, 'update']);
    Route::get('request_payments/user_search', [RequestPaymentController::class, 'requestpaymentsUserSearch']);
    Route::get('request_payments/csv', [RequestPaymentController::class, 'requestpaymentCsv']);
    Route::get('request_payments/pdf', [RequestPaymentController::class, 'requestpaymentPdf']);

    // Revenues
    Route::get('revenues', [RevenueController::class, 'revenues_list'])->middleware(['permission:view_revenue']);
    Route::get('revenues/user_search', [RevenueController::class, 'revenuesUserSearch']);
    Route::get('revenues/csv', [RevenueController::class, 'revenueCsv']);
    Route::get('revenues/pdf', [RevenueController::class, 'revenuePdf']);

    // disputes
    Route::get('disputes', [DisputeController::class, 'index'])->middleware(['permission:view_disputes']);

    Route::get('dispute/add/{id}', [DisputeController::class, 'add']);
    Route::post('dispute/open', [DisputeController::class, 'store']);

    Route::get('dispute/discussion/{id}', [DisputeController::class, 'discussion'])->middleware(['permission:edit_dispute']);
    Route::post('dispute/reply', [DisputeController::class, 'storeReply']);
    Route::post('dispute/change_reply_status', [DisputeController::class, 'changeReplyStatus']);
    Route::get('disputes_user_search', [DisputeController::class, 'disputesUserSearch']);
    Route::get('disputes_transactions_search', [DisputeController::class, 'disputesTransactionsSearch']);
    Route::get('dispute/download/{file}', [DisputeController::class, 'download']);

    // Tickets
    Route::get('tickets/list', [TicketController::class, 'index'])->middleware(['permission:view_tickets']);
    Route::get('tickets/add', [TicketController::class, 'create'])->middleware(['permission:add_ticket']);
    Route::post('tickets/store', [TicketController::class, 'store']);
    Route::get('ticket_user_search', [TicketController::class, 'ticketUserSearch']);
    Route::get('tickets/reply/{id}', [TicketController::class, 'reply'])->middleware(['permission:edit_ticket']);
    Route::post('tickets/change_ticket_status', [TicketController::class, 'change_ticket_status']);
    Route::post('tickets/reply/store', [TicketController::class, 'adminTicketReply']);
    Route::post('tickets/reply/update', [TicketController::class, 'replyUpdate']);
    Route::post('tickets/reply/delete', [TicketController::class, 'replyDelete']);
    Route::get('tickets/edit/{id}', [TicketController::class, 'edit'])->middleware(['permission:edit_ticket']);
    Route::post('tickets/update', [TicketController::class, 'update']);
    Route::get('tickets/delete/{id}', [TicketController::class, 'delete'])->middleware(['permission:delete_ticket']);
    Route::get('ticket/download/{file}', [TicketController::class, 'download']);

    // Email Templates
    Route::get('template/{alias?}', [EmailTemplateController::class, 'index'])->middleware(['permission:view_email_template'])->name('email.template.index');
    Route::post('template_update/{alias}', [EmailTemplateController::class, 'update'])->middleware(['permission:edit_email_template'])->name('email.template.update');

    Route::get('sms-template/{alias?}', [SmsTemplateController::class, 'index'])->middleware(['permission:view_sms_template'])->name('sms.template.index');
    Route::post('sms-template/update/{alias}', [SmsTemplateController::class, 'update'])->middleware(['permission:edit_sms_template'])->name('sms.template.update');

    // Activity Logs
    Route::get('activity_logs', [ActivityLogController::class, 'activities_list'])->middleware(['permission:view_activity_log']);

    // currencies
    Route::get('settings/currency', [CurrencyController::class, 'index'])->middleware(['permission:view_currency']);
    Route::match(array('GET', 'POST'), 'settings/add_currency', [CurrencyController::class, 'add'])->middleware(['permission:add_currency']);
    Route::match(array('GET', 'POST'), 'settings/edit_currency/{id}', [CurrencyController::class, 'update'])->middleware(['permission:edit_currency']);
    Route::get('settings/delete_currency/{id}', [CurrencyController::class, 'delete'])->middleware(['permission:delete_currency'])->middleware('demo_check');
    Route::post('currency/image_delete', [CurrencyController::class, 'deleteImage']);
    Route::post('settings/currency/delete-currency-logo', [CurrencyController::class, 'deleteCurrencyLogo']);

    // FeesLimit
    Route::get('settings/feeslimit/{tab}/{id}', [FeesLimitController::class, 'limitList'])->middleware(['permission:edit_currency']);
    Route::post('settings/get-feeslimit-details', [FeesLimitController::class, 'getFesslimitDetails']);
    Route::post('settings/feeslimit/update-deposit-limit', [FeesLimitController::class, 'updateDepositLimit']);
    Route::post('settings/get-specific-currency-details', [FeesLimitController::class, 'getSpecificCurrencyDetails']);

    //Currency PaymentMethod Settings
    Route::get('settings/payment-methods/{tab}/{id}', [CurrencyPaymentMethodController::class, 'paymentMethodList'])->middleware(['permission:edit_currency']);
    Route::post('settings/payment-methods/update-paymentMethod-Credentials', [CurrencyPaymentMethodController::class, 'updatePaymentMethodCredentials']);
    Route::post('settings/get-payment-methods-details', [CurrencyPaymentMethodController::class, 'getPaymentMethodsDetails']);
    Route::post('settings/get-payment-methods-specific-currency-details', [CurrencyPaymentMethodController::class, 'getPaymentMethodsSpecificCurrencyDetails']);

    //bank
    Route::post('settings/payment-methods/add-bank', [CurrencyPaymentMethodController::class, 'addBank']);
    Route::post('settings/payment-methods/update-bank', [CurrencyPaymentMethodController::class, 'updateBank']);
    Route::post('settings/payment-methods/delete-bank', [CurrencyPaymentMethodController::class, 'deleteBank']);
    Route::post('settings/payment-methods/getCpmId', [CurrencyPaymentMethodController::class, 'getCpmId']);
    Route::post('settings/payment-methods/show-bank-details', [CurrencyPaymentMethodController::class, 'showbankDetails']);
    Route::post('settings/payment-methods/delete-bank-logo', [CurrencyPaymentMethodController::class, 'deleteBankLogo']);

    //settings
    Route::match(array('GET', 'POST'), 'settings', [SettingController::class, 'general']);

    Route::post('settings/update-sidebar-company-logo', [SettingController::class, 'updateSideBarCompanyLogo']);
    Route::post('settings/check-sms-settings', [SettingController::class, 'checkSmsGatewaySettings']);
    Route::post('settings/delete-logo', [SettingController::class, 'deleteSettingLogo']);
    Route::post('settings/delete-dark-logo', [SettingController::class, 'deleteSettingDarkLogo']);
    Route::post('settings/delete-favicon', [SettingController::class, 'deleteSettingFavicon']);

    // Admin Security Settings
    Route::match(['get', 'post'], 'settings/admin-security-settings', [SettingController::class, 'adminSecuritySettings'])->middleware(['permission:view_admin_security', 'demo_check']);

    //social_links
    Route::match(array('GET', 'POST'), 'settings/social_links', [SettingController::class, 'social_links'])->middleware(['permission:view_social_links']);

    Route::get('settings/theme-set/{theme}', [SettingController::class, 'themeSet'])->middleware(['permission:view_social_links']);

    //api_informations
    Route::match(array('GET', 'POST'), 'settings/api_informations', [SettingController::class, 'api_informations'])->middleware(['permission:view_api_credentials']);

    // currency conversion rate api
    Route::match(array('GET', 'POST'), 'settings/currency-conversion-rate-api', [SettingController::class, 'currencyConversionRateApi'])->middleware(['permission:view_conversion_rate_api']);

    //appstore credentials
    Route::get('settings/app-store-credentials', [AppStoreCredentialController::class, 'getAppStoreCredentials'])->middleware(['permission:view_appstore_credentials']);
    Route::post('settings/app-store-credentials/update-google-credentials', [AppStoreCredentialController::class, 'updateGoogleCredentials']);
    Route::post('settings/app-store-credentials/update-apple-credentials', [AppStoreCredentialController::class, 'updateAppleCredentials']);
    Route::post('settings/app-store-credentials/delete-playstore-logo', [AppStoreCredentialController::class, 'deletePlaystoreLogo']);
    Route::post('settings/app-store-credentials/delete-appstore-logo', [AppStoreCredentialController::class, 'deleteAppStoreLogo']);

    //email_settings
    Route::match(array('GET', 'POST'), 'settings/email', [SettingController::class, 'email'])->middleware(['permission:view_email_setting']);

    // Route::match(array('GET', 'POST'), 'settings/sms', [SettingController::class, 'sms'])->middleware(['permission:view_sms_setting']);

    Route::match(array('GET', 'POST'), 'settings/sms/{type}', [SettingController::class, 'sms'])->middleware(['permission:view_sms_setting']);

    //countries
    Route::get('settings/country', [CountryController::class, 'index'])->middleware(['permission:view_country']);
    Route::match(array('GET', 'POST'), 'settings/add_country', [CountryController::class, 'add'])->middleware(['permission:add_country']);
    Route::match(array('GET', 'POST'), 'settings/edit_country/{id}', [CountryController::class, 'update'])->middleware(['permission:edit_country']);
    Route::get('settings/delete_country/{id}', [CountryController::class, 'delete'])->middleware(['permission:delete_country']);

    //languages
    Route::get('settings/language', [LanguageController::class, 'index'])->middleware(['permission:view_language']);
    Route::match(array('GET', 'POST'), 'settings/add_language', [LanguageController::class, 'add'])->middleware(['permission:add_language']);
    Route::match(array('GET', 'POST'), 'settings/edit_language/{id}', [LanguageController::class, 'update'])->middleware(['permission:edit_language']);
    Route::get('settings/delete_language/{id}', [LanguageController::class, 'delete'])->middleware(['permission:delete_language']);
    Route::post('settings/language/delete-flag', [LanguageController::class, 'deleteFlag']);

    //Merchant Group/Roles
    Route::get('settings/merchant-group', [MerchantGroupController::class, 'index'])->middleware(['permission:view_merchant_group']);
    Route::match(array('GET', 'POST'), 'settings/add-merchant-group', [MerchantGroupController::class, 'add'])->middleware(['permission:add_merchant_group']);
    Route::match(array('GET', 'POST'), 'settings/edit-merchant-group/{id}', [MerchantGroupController::class, 'update'])->middleware(['permission:edit_merchant_group']);
    Route::get('settings/delete-merchant-group/{id}', [MerchantGroupController::class, 'delete'])->middleware(['permission:delete_merchant_group']);

    //User Group/Roles
    Route::get('settings/user_role', [UsersRoleController::class, 'index'])->middleware(['permission:view_group']);
    Route::match(array('GET', 'POST'), 'settings/add_user_role', [UsersRoleController::class, 'add'])->middleware(['permission:add_group']);
    Route::match(array('GET', 'POST'), 'settings/edit_user_role/{id}', [UsersRoleController::class, 'update'])->middleware(['permission:edit_group', 'demo_check']);
    Route::get('settings/delete_user_role/{id}', [UsersRoleController::class, 'delete'])->middleware(['permission:delete_group']);
    Route::get('settings/roles/check-user-permissions', [UsersRoleController::class, 'checkUserPermissions']);

    //Admin Group/Roles
    Route::get('settings/roles', [RoleController::class, 'index'])->middleware(['permission:view_role']);
    Route::match(array('GET', 'POST'), 'settings/add_role', [RoleController::class, 'add'])->middleware(['permission:add_role']);
    Route::match(array('GET', 'POST'), 'settings/edit_role/{id}', [RoleController::class, 'update'])->middleware(['permission:edit_role']);
    Route::get('settings/delete_role/{id}', [RoleController::class, 'delete'])->middleware(['permission:delete_role']);
    Route::post('settings/roles/duplicate-role-check', [RoleController::class, 'duplicateRoleCheck']);

    //Database Backup
    Route::get('settings/backup', [BackupController::class, 'index'])->middleware(['permission:view_database_backup']);
    Route::get('backup/save', [BackupController::class, 'add'])->middleware(['permission:add_database_backup']);
    Route::get('backup/download/{id}', [BackupController::class, 'download'])->middleware(['permission:edit_database_backup']);

    //metas
    Route::get('settings/metas', [MetaController::class, 'index'])->middleware(['permission:view_meta']);
    Route::match(array('GET', 'POST'), 'settings/edit_meta/{id}', [MetaController::class, 'update'])->middleware(['permission:edit_meta']);

    //Pages
    Route::get('settings/pages', [PagesController::class, 'index'])->middleware(['permission:view_page']);
    Route::get('settings/page/add', [PagesController::class, 'add'])->middleware(['permission:add_page']);
    Route::post('settings/page/store', [PagesController::class, 'store']);
    Route::get('settings/page/edit/{id}', [PagesController::class, 'edit'])->middleware(['permission:edit_page']);
    Route::post('settings/page/update', [PagesController::class, 'update']);
    Route::get('settings/page/delete/{id}', [PagesController::class, 'delete'])->middleware(['permission:delete_page']);

    //Preferences
    Route::get('settings/preference', [SettingController::class, 'preference'])->middleware(['permission:view_preference']);
    Route::post('save-preference', [SettingController::class, 'savePreference'])->middleware(['permission:edit_preference']);

    //Enable Woocommerce
    Route::match(array('GET', 'POST'), 'settings/enable-woocommerce', [SettingController::class, 'enableWoocommerce'])->middleware(['permission:view_enable_woocommerce']);

    //Notifications
    Route::get('settings/notification-types', [NotificationTypeController::class, 'index'])->middleware(['permission:view_notification_type']);
    Route::get('settings/notification-types/edit/{id}', [NotificationTypeController::class, 'edit'])->middleware(['permission:edit_notification_type']);
    Route::post('settings/notification-types/update/{id}', [NotificationTypeController::class, 'update'])->middleware(['permission:edit_notification_type']);
    Route::post('settings/notification-type-name/check', [NotificationTypeController::class, 'uniqueNotificationTypeNameCheck']);
    Route::get('settings/notification-settings/{type}', [NotificationSettingController::class, 'index'])->middleware(['permission:view_notification_setting']);
    Route::post('settings/notification-settings/update', [NotificationSettingController::class, 'update'])->middleware(['permission:edit_notification_setting']);

    // Addon
    Route::match(array('GET', 'POST'), '/custom/addons', [AddonController::class, 'index']);
    Route::get('/custom/addon/activation/{status}/{id}', [AddonController::class, 'activation']);

    // ModuleManager
    Route::get('module-manager/addons', [ModuleManagerController::class, 'index'])->middleware(['permission:view_addon_manager']);

    // Crypto Providers
    Route::get('crypto-providers/{provider?}', [CryptoProviderController::class, 'index'])->name('admin.crypto_providers.list')->middleware(['permission:view_crypto_provider']);
    Route::post('crypto-provider/{provider}/status-change', [CryptoProviderController::class, 'statusChange'])->name('admin.crypto_providers.status_change')->middleware(['permission:edit_crypto_provider']);

    // System info
    Route::get('system-info', [SystemInfoController::class, 'index'])->name('systemInfo.index');
});
