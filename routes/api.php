<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:api'])->group(function() { 
    Route::get('/user/info', 'UserController@info')->name('user.info');

    Route::resource('/emails', 'EmailController', ['except'=>[
        'update',
        'edit',
        'create',
        'show'
    ]]);

    Route::fallback(function(){
        return response()->json(['message' => 'Not Found.'], 404);
    })->name('api.fallback.404');

});

Route::post('/emails/{email}','RevealEmailController@reveal')->name('emails.check');