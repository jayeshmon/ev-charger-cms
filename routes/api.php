<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Display;
use App\Http\Controllers\Save;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('getcharger', 'App\Http\Controllers\Display@getcharger')->name('getcharger');
Route::get('getstation', 'App\Http\Controllers\Display@getstation')->name('getstation');
Route::get('getpartner', 'App\Http\Controllers\Display@getpartner')->name('getpartner');
Route::get('getreservation', 'App\Http\Controllers\Display@getreservation')->name('getreservation');
Route::get('getalarm', 'App\Http\Controllers\Display@getalarm')->name('getalarm');
Route::get('getchargetransaction', 'App\Http\Controllers\Display@getchargetransaction')->name('getchargetransaction');
Route::get('gettransaction', 'App\Http\Controllers\Display@gettransaction')->name('gettransaction');
Route::get('getoem', 'App\Http\Controllers\Display@getoem')->name('getoem');
Route::get('getdevicemodel', 'App\Http\Controllers\Display@getdevicemodel')->name('getdevicemodel');
Route::get('gettariff', 'App\Http\Controllers\Display@gettariff')->name('gettariff');
Route::get('gettax', 'App\Http\Controllers\Display@gettax')->name('gettax');
Route::get('getuser', 'App\Http\Controllers\Display@getuser')->name('getuser');

Route::get('postcharger', 'App\Http\Controllers\Save@postcharger')->name('postcharger');
Route::get('poststation', 'App\Http\Controllers\Save@poststation')->name('poststation');
Route::get('postpartner', 'App\Http\Controllers\Save@postpartner')->name('postpartner');
Route::get('postreservation', 'App\Http\Controllers\Save@postreservation')->name('postreservation');
Route::get('postalarm', 'App\Http\Controllers\Save@postalarm')->name('postalarm');
Route::get('postcharpostransaction', 'App\Http\Save\Display@postcharpostransaction')->name('postcharpostransaction');
Route::get('posttransaction', 'App\Http\Controllers\Save@posttransaction')->name('posttransaction');
Route::get('postoem', 'App\Http\Controllers\Save@postoem')->name('postoem');
Route::get('postdevicemodel', 'App\Http\Controllers\Save@postdevicemodel')->name('postdevicemodel');
Route::get('posttariff', 'App\Http\Controllers\Save@posttariff')->name('posttariff');
Route::get('posttax', 'App\Http\Controllers\Save@posttax')->name('posttax');
Route::get('postuser', 'App\Http\Controllers\Save@postuser')->name('postuser');


//By charger



//

