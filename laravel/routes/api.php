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
Route::get('sync/index', 'SyncController@index');
Route::get('sync/products', 'SyncController@getProducts');
Route::get('sync/orders', 'SyncController@getOrders');
Route::group(['middleware' => 'auth'], function(){
});