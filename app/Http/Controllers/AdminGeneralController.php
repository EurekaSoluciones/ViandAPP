<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminGeneralController extends Controller
{
    public function obtenerStockdePersona(Request $request)
    {
        $idPersona=$request->persona;
        $fecha=Carbon::parse(strtotime(str_replace('/', '-',$request->fecha)));

        $stock =Stock::devolverStockdePersona($idPersona, $fecha);

        $arrDatos=["stock"=>$stock];
        return response($arrDatos);
    }
}
