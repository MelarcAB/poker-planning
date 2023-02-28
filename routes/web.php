<?php

use Illuminate\Support\Facades\Route;




Route::get('/', 'App\Http\Controllers\PublicController@index')->name('index');


//login y registrho
Auth::routes();

Route::controller(App\Http\Controllers\Auth\LoginController::class)->group(function () {
    Route::get('/auth/google', 'redirectToGoogle')->name('google.login');
    Route::get('/auth/google/callback', 'handleGoogleCallback')->name('google.callback');
});


Route::controller(App\Http\Controllers\HomeController::class)->group(function () {
    Route::get('/home', 'home')->name('home');
    Route::get('/config', 'config')->name('config');
    Route::get('/my-groups', 'my_groups')->name('my-groups');
    Route::get('/search-group', 'search_group')->name('search_group');
    Route::get('/my-decks', 'my_decks')->name('my-decks');
    Route::get('/deck', 'new_deck')->name('new-deck');
    Route::get('/deck/{slug}', 'new_deck')->name('deck');
    //post
    Route::post('/save-deck', 'save_deck')->name('save-deck');
    Route::post('/save-config', 'saveConfig')->name('save-config');
});

//controller GerenteController
Route::controller(App\Http\Controllers\GerenteController::class)->group(function () {
    Route::get('/new-group', 'formNewGroup')->name('form-new-group');
    Route::get('/{group_slug}/new-room', 'formNewRoom')->name('form-new-room');
    Route::post('/save-room', 'saveRoom')->name('save-room');
});

//groups controller
Route::controller(App\Http\Controllers\GroupsController::class)->group(function () {
    Route::post('/save-group', 'saveGroup')->name('save-group');
    Route::get('/group/{slug}', 'group')->name('group');
    Route::get('/{group_slug}/{room_slug}', 'room')->name('group.room');
});
