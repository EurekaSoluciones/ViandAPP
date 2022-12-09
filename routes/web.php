<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Http\Controllers\PersonaController;
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

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index')->name('home');;

//Route::middleware([
//    'auth:sanctum',
//    config('jetstream.auth_session'),
//    'verified'
//])->group(function () {
//    Route::get('/dashboard', 'HomeController@index')->name('welcome');
//});

//Route::middleware([
//    'auth:sanctum',
//    config('jetstream.auth_session'),
//    'verified'
//])->group(function () {
//    Route::get('/dashboard', function () {
//        return view('welcome');
//    })->name('welcome');
//});

Route::resource('personas', 'PersonaController');
Route::resource('comercios', 'ComercioController');
Route::resource('usuarios', 'UserController');

/*Acciones de Comercios*/
Route::get('consumir', [Controllers\StockController::class, 'consumir'])->name('consumir');
Route::POST('generarconsumo', [Controllers\StockController::class, 'generarconsumo'])->name('generarconsumo');

Route::get('cerrarLote', [Controllers\ComercioController::class, 'cerrarLote'])->name('cerrarLote');
Route::POST('generarCierreLote', [Controllers\ComercioController::class, 'generarCierreLote'])->name('generarCierreLote');
Route::get('detalleLote/{id}', [Controllers\ComercioController::class, 'detalleLote'])->name('detalleLote');

Route::get('consumosPendientes', [Controllers\ComercioController::class, 'consumosPendientesDeRendir'])->name('consumosPendientes');


/*Acciones de Administradores*/
Route::POST('asignacionexcel', [Controllers\StockController::class, 'import'])->name('asignacionexcel');
Route::get('importarexcel', [Controllers\StockController::class, 'index'])->name('importarexcel');
Route::POST('confirmarimportacion', [Controllers\StockController::class, 'confirmarImportacion'])->name('confirmarimportacion');
Route::get('aumentarstock', [Controllers\StockController::class, 'aumentarstock'])->name('aumentarstock');
Route::POST('generaraumento', [Controllers\StockController::class, 'generaraumento'])->name('generaraumento');
Route::get('disminuirstock', [Controllers\StockController::class, 'disminuirstock'])->name('disminuirstock');
Route::POST('generardisminucion', [Controllers\StockController::class, 'generardisminucion'])->name('generardisminucion');
Route::get('pedidogrupal', [Controllers\AdminController::class, 'pedidogrupal'])->name('pedidogrupal');
Route::POST('generarpedidogrupal', [Controllers\AdminController::class, 'generarpedidogrupal'])->name('generarpedidogrupal');
Route::get('detallePedido/{id}', [Controllers\AdminController::class, 'detallePedido'])->name('detallePedido');


Route::get('/usuarios/inactivar/{id}', 'UserController@inactivar')->name('usuarios.inactivar');
Route::post('/usuarios/reactivar/{id}', 'UserController@reactivar')->name('usuarios.reactivar');
Route::get('/usuarios/reiniciarclave/{id}', 'UserController@reiniciarclave')->name('usuarios.reiniciarclave');
Route::post('/usuarios/guardarclave', 'UserController@guardarclave')->name('usuarios.guardarclave');

/*Acciones de Personas*/
Route::get('/usuarios/cambiarcontrasenia', 'UserController@cambiarclave')->name('usuarios.cambiarcontrasenia');



/*Llamadas Ajax para obtener valores adicionales*/
Route::get('/obtenerStockdePersona', 'AdminGeneralController@obtenerStockdePersona')->name('obtenerStockdePersona');
