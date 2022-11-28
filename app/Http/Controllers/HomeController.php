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
                return AdminController::mostrarWelcome();
                break;

        }


    }

}
