<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ComercioController;
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

Route::apiResource('v1/comercios', ComercioController::class)
    ->middleware('auth:sanctum')
    ->only(['index','show']);

Route::post('login', [App\Http\Controllers\Api\LoginController::class, 'login']);

Route::get('v1/helloworld', [App\Http\Controllers\Api\V1\TestController::class, 'helloworld']);

Route::post('v1/comercios/consumir', [App\Http\Controllers\Api\V1\ComercioController::class, 'consumir']);

