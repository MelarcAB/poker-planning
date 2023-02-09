<?php

use Illuminate\Support\Facades\Route;




Route::get('/', 'App\Http\Controllers\PublicController@index')->name('index');



Route::controller(App\Http\Controllers\HomeController::class)->group(function () {
    Route::get('/home', 'home')->name('home');
    Route::get('/my-groups', 'my_groups')->name('my-groups');
});

//controller GerenteController
Route::controller(App\Http\Controllers\GerenteController::class)->group(function () {
    Route::get('/new-group', 'formNewGroup')->name('form-new-group');
});



//login y registrho
Auth::routes();
Route::controller(App\Http\Controllers\Auth\LoginController::class)->group(function () {
    Route::get('/auth/google', 'redirectToGoogle')->name('google.login');
    Route::get('/auth/google/callback', 'handleGoogleCallback')->name('google.callback');
});
