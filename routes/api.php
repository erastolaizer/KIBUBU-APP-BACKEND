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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('user', 'UserController@register');
Route::post('user/login', 'UserController@login');
Route::post('user/forgetPass', 'UserController@forgetPass');
Route::post('lengo', 'LengoController@createLengo');
Route::get('lengo/{user_id}', 'LengoController@getMalengo');
Route::post('deposit', 'DepositController@Deposit');
Route::get('balances/{user_id}', 'BalanceController@Balances');
Route::get('history/{user_id}', 'DepositController@history');
Route::post('withdraw', 'BalanceController@withdraw');
