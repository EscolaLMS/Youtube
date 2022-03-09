<?php

use EscolaLms\Youtube\Http\Controllers\GoogleController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api'], 'prefix' => 'api/admin'], function () {
    Route::post('g-token/generate', [GoogleController::class, 'generateUrl']);
});
Route::group(['prefix' => 'api'], function () {
    Route::get('refresh-token', [GoogleController::class, 'setRefreshToken']);
});
