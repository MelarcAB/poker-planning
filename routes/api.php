<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//use APIController
use App\Http\Controllers\APIController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



//controller APIController
Route::controller(APIController::class)->middleware(\App\Http\Middleware\VerifyCsrfTokenCustom::class)
    ->group(function () {
        Route::post('/update-group-code', 'updateGroupCode')->name('update-group-code');
    });
