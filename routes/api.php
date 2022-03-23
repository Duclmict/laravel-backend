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

Route::post('login', 'Api\UserController@login');
Route::post('user/g-login', 'Api\UserController@loginViaGoole');
Route::post('user/resetPassword', 'Api\UserController@resetPassword');
Route::post('user/confirmReset', 'Api\UserController@confirmReset');

Route::group(['middleware' => ['jwt.auth']], function () {

    Route::group(['middleware' => ['api.role']], function() {
        // ユーザ
        Route::get('users/search', 'Api\UserController@search');
        Route::get('user/profile', 'Api\UserController@getProfile');
        Route::get('user/logout', 'Api\UserController@logout');
        Route::post('user/profile', 'Api\UserController@updateProfile');
        Route::post('user/changePassword', 'Api\UserController@changePassword');
        Route::resource('users', 'Api\UserController');
    });

});

Route::fallback('BaseController@fallback');