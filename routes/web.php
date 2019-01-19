<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/emails/{email}','RevealEmailController@show')->name('emails.reveal');

Route::get('/users/{user}/change-email', 'UserController@changeEmail')->name('user.changeEmail')->middleware(['signed','auth']);

Route::get('login/{provider}', 'Auth\LoginController@redirectToProvider')->name('login.socialite');
Route::get('login/{provider}/callback', 'Auth\LoginController@handleProviderCallback');

// below must be the last one
Route::get('/{vue_capture?}', 'HomeController@index')->where('vue_capture', '[\/\w\.-]*');