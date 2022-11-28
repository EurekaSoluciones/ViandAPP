<?php

namespace App\Http\Controllers;

use App\Models\Comercio;
use App\Models\Persona;
use App\Models\StockMovimiento;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public static function mostrarWelcome()
    {
        $fecha= \Illuminate\Support\Carbon::now();

        $user=auth()->user();

        $empleados=count(Persona:: where('activo', true)->get());

        $comercios=count(Comercio:: where('activo', true)->get());

        $desayunos=count(StockMovimiento::whereMonth('fecha', $fecha->month)
            ->whereYear('fecha', $fecha->year)
            ->where('tipomovimiento_id', 2)
            ->where('articulo_id',1)->get());

        $viandas=count(StockMovimiento::whereMonth('fecha', $fecha->month)
            ->whereYear('fecha', $fecha->year)
            ->where('tipomovimiento_id', 2)
            ->where('articulo_id',2)->get());

        return view('welcome')
            ->with('empleados',$empleados)
            ->with('comercios',$comercios)
            ->with('viandas', $viandas)
            ->with('desayunos',$desayunos);

    }
}