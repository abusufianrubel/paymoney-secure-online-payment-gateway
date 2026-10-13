<?php

use Illuminate\Support\Facades\Route;
use Modules\KycVerification\Http\Controllers\Api\VerificationController;

Route::group(['middleware' => ['auth:api-v2', 'check-user-inactive']],function () {
    Route::controller(VerificationController::class)->prefix('')->group(function () {
        Route::get('identity-verification-requirements', 'identityRequirements');
        Route::get('identity-verification-data', 'identityData');
        Route::post('process-identity-verification', 'processIdentity');
        Route::get('address-verification-requirements', 'addressRequirements');
        Route::get('address-verification-data', 'addressData');
        Route::post('process-address-verification', 'processAddress');
    });
});
