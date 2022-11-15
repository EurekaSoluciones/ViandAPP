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
Route::get('/home', 'HomeController@index');

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

Route::POST('asignacionexcel', [Controllers\StockController::class, 'import'])->name('asignacionexcel');
Route::get('importarexcel', [Controllers\StockController::class, 'index'])->name('importarexcel');
Route::POST('confirmarimportacion', [Controllers\StockController::class, 'confirmarImportacion'])->name('confirmarimportacion');
Route::get('consumir', [Controllers\StockController::class, 'consumir'])->name('consumir');
Route::POST('generarconsumo', [Controllers\StockController::class, 'generarconsumo'])->name('generarconsumo');
Route::get('aumentarstock', [Controllers\StockController::class, 'aumentarstock'])->name('aumentarstock');
Route::POST('generaraumento', [Controllers\StockController::class, 'generaraumento'])->name('generaraumento');
Route::get('disminuirstock', [Controllers\StockController::class, 'disminuirstock'])->name('disminuirstock');
Route::POST('generardisminucion', [Controllers\StockController::class, 'generardisminucion'])->name('generardisminucion');

/*Llamadas Ajax para obtener valores adicionales*/
Route::get('/obtenerStockdePersona', 'AdminGeneralController@obtenerStockdePersona')->name('obtenerStockdePersona');
