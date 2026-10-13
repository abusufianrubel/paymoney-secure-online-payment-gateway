<?php

use Illuminate\Support\Facades\Route;
use Modules\Upgrader\Http\Controllers\SystemUpdateController;

Route::group(config('addons.route_group.authenticated.admin'), function() {
    Route::controller(SystemUpdateController::class)->group(function () {
        Route::match(['GET', 'POST'], 'system-update', 'upgrade')->name('systemUpdate.upgrade');
        Route::get('version/check', 'checkVersion')->name('version.check');
        Route::post('version/download', 'downloadVersion')->name('version.download')->middleware('demo_check');
    });
});
