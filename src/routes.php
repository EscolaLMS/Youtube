<?php

use EscolaLms\Youtube\Http\Controllers\GoogleController;

Route::group(['middleware' => ['auth:api'], 'prefix' => 'api/admin'], function () {
    Route::post('g-token/generate', [GoogleController::class, 'generateUrl']);
    Route::resource('webinars', WebinarController::class);
});
