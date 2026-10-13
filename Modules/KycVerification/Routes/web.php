<?php

use Illuminate\Support\Facades\Route;
use Modules\KycVerification\Http\Controllers\{
    Admin\SettingController,
    Admin\ProviderController,
    Admin\VerificationController,
    Admin\CredentialSettingController,
    User\VerificationController as UserVerificationController
};

Route::group(config('addons.route_group.authenticated.admin'), function () {
    // Settings route
    Route::controller(SettingController::class)->as('admin.kyc.settings.')->group(function () {
        Route::get('kyc/settings', 'create')->name('create')->middleware(['permission:view_kyc_setting']);
        Route::post('kyc/settings', 'store')->name('store')->middleware(['permission:edit_kyc_setting']);
    });

    // Providers route
    Route::controller(ProviderController::class)->as('admin.kyc.providers.')->group(function () {
        Route::get('kyc/providers', 'index')->name('index')->middleware(['permission:view_kyc_provider']);
        Route::get('kyc/providers/{provider}', 'edit')->name('edit')->where('provider', '[0-9]+')->middleware(['permission:edit_kyc_provider']);
        Route::put('kyc/providers/update/{provider}', 'update')->where('provider', '[0-9]+')->name('update');
    });

    // Verifications route
    Route::controller(VerificationController::class)->as('admin.kyc.verifications.')->group(function () {
        Route::get('kyc/verifications', 'index')->name('index')->middleware(['permission:view_kyc_verification']);
        Route::get('kyc/verifications/csv', 'csv')->name('csv');
        Route::get('kyc/verifications/pdf', 'pdf')->name('pdf');
        Route::get('kyc/verifications/{verification}', 'edit')->where('verification', '[0-9]+')->name('edit')->middleware(['permission:edit_kyc_verification']);
        Route::put('kyc/verifications/update/{verification}', 'update')->where('verification', '[0-9]+')->name('update');
    });

    // credentials setting route
    Route::controller(CredentialSettingController::class)->as('admin.kyc.credentials.')->group(function () {
        Route::get('kyc/get-active-provider-credential-settings', 'activeProviderCredentialSetting')->name('active-provider-setting');
    });
});

Route::group(config('addons.route_group.authenticated.user'), function () {
    // Verifications route
    Route::controller(UserVerificationController::class)->as('user.kyc.verifications.')->prefix('user/kyc')->group(function () {
        Route::get('verifications', 'initiate')->name('initiate');
        Route::post('verifications/process', 'processVerification')->name('process');
        Route::get('address-verifications', 'addressVerify')->name('address');
        Route::post('process-address-verifications', 'processAddressVerification')->name('process.address');
        Route::get('proof-download/{type}/{fileName}', 'download')->name('proof.download');
    });
});
