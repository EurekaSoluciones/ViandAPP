<?php

namespace App\Http\Controllers;

use App\Models\Comercio;
use App\Models\Persona;
use App\Models\StockMovimiento;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $query="";

        $fecha= \Illuminate\Support\Carbon::now();

        $user=auth()->user();

        switch ($user->perfil_id)
        {
            case config('global.PERFIL_Comercio'):
                return ComercioController::mostrarWelcome();

                break;

            case config('global.PERFIL_Persona'):
                return PersonaController::mostrarWelcome();
                break;

            default:
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

            break;

        }


    }
}
