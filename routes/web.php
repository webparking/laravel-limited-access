<?php

use Illuminate\Support\Facades\Route;

Route::name('LimitedAccess::')->middleware([
    'web',
    'throttle:5,1',
])->group(function () {
    Route::get(
        'limited-access-login',
        '\Webparking\LimitedAccess\Http\Controllers\LimitedAccessController@login'
    )->name('login');

    Route::post(
        'limited-access-login',
        '\Webparking\LimitedAccess\Http\Controllers\LimitedAccessController@validate'
    )->name('verify');
});
