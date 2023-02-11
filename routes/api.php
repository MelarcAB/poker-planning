<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//use APIController
use App\Http\Controllers\APIController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



//controller APIController
Route::controller(APIController::class)->middleware(['jwt.auth'])
    ->group(function () {
        Route::post('/update-group-code', 'updateGroupCode')->name('update-group-code');
        Route::post('/check-code', 'checkCode')->name('check-code');
    });
