<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ComercioController;
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

Route::apiResource('comercios', ComercioController::class)
    ->middleware('auth:sanctum')
    ->only(['index','show']);

Route::post('login', [App\Http\Controllers\Api\LoginController::class, 'login']);

Route::post('loginComercio', [App\Http\Controllers\Api\LoginController::class, 'loginComercio']);

Route::post('helloWorld', [App\Http\Controllers\Api\TestController::class, 'helloWorld']);

Route::post('helloWorldConAuth',  [App\Http\Controllers\Api\TestController::class, 'helloWorldConAuth'])
    ->middleware('auth:sanctum') ;

Route::post('comercios/consumir', [App\Http\Controllers\Api\ComercioController::class, 'consumir']);

