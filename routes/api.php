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
Route::post('loginPersona', [App\Http\Controllers\Api\LoginController::class, 'loginPersona']);

Route::post('helloWorld', [App\Http\Controllers\Api\TestController::class, 'helloWorld']);

//Route::post('helloWorldConAuth',  [App\Http\Controllers\Api\TestController::class, 'helloWorldConAuth'])
//    ->middleware('auth:sanctum') ;

Route::post('helloWorldConAuth',  [App\Http\Controllers\Api\TestController::class, 'helloWorldConAuth'])
    ->middleware('App\Http\Middleware\EureAuthApis');

/********************************************************************/
/*OPERACIONES DE COMERCIOS*/
/********************************************************************/

Route::post('comercios/consumir', [App\Http\Controllers\Api\ComercioController::class, 'consumir'])
    ->middleware('App\Http\Middleware\EureAuthApis');

Route::post('comercios/anularConsumo', [App\Http\Controllers\Api\ComercioController::class, 'anularConsumo'])
    ->middleware('App\Http\Middleware\EureAuthApis');

Route::post('comercios/consumosPendientesDeRendir', [App\Http\Controllers\Api\ComercioController::class, 'consumosPendientesDeRendir'])
    ->middleware('App\Http\Middleware\EureAuthApis');

Route::post('comercios/cerrarLote', [App\Http\Controllers\Api\ComercioController::class, 'cerrarLote'])
    ->middleware('App\Http\Middleware\EureAuthApis');

Route::post('comercios/pedidosGrupales', [App\Http\Controllers\Api\ComercioController::class, 'pedidosGrupales'])
    ->middleware('App\Http\Middleware\EureAuthApis');

Route::post('comercios/confirmarPedidoGrupal', [App\Http\Controllers\Api\ComercioController::class, 'confirmarPedidoGrupal'])
    ->middleware('App\Http\Middleware\EureAuthApis');

Route::post('comercios/rechazarPedidoGrupal', [App\Http\Controllers\Api\ComercioController::class, 'rechazarPedidoGrupal'])
    ->middleware('App\Http\Middleware\EureAuthApis');

/********************************************************************/
/*OPERACIONES DE PERSONAS*/
/********************************************************************/

Route::post('personas/obtenerPersonaByQr', [App\Http\Controllers\Api\PersonaController::class, 'devolverPersonaxQR'])
    ->middleware('App\Http\Middleware\EureAuthApis');

Route::post('personas/obtenerPersonaByToken', [App\Http\Controllers\Api\PersonaController::class, 'devolverPersonaxToken'])
    ->middleware('App\Http\Middleware\EureAuthApis');

Route::post('personas/generarNuevoQr', [App\Http\Controllers\Api\PersonaController::class, 'generarNuevoQr'])
    ->middleware('App\Http\Middleware\EureAuthApis');

Route::post('cambiarClave', [App\Http\Controllers\Api\LoginController::class, 'cambiarClave'])
    ->middleware('App\Http\Middleware\EureAuthApis');

Route::post('personas/notificaciones', [App\Http\Controllers\Api\PersonaController::class, 'notificaciones'])
    ->middleware('App\Http\Middleware\EureAuthApis');

Route::post('personas/misConsumos', [App\Http\Controllers\Api\PersonaController::class, 'misconsumos'])
    ->middleware('App\Http\Middleware\EureAuthApis');
