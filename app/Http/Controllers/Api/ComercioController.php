<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AdminGeneralController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\StockController;
use App\Http\Resources\V1\ComercioResource;
use App\Http\Resources\v1\StockMovimientoResource;
use App\Models\Articulo;
use App\Models\Comercio;
use App\Models\Persona;
use App\Models\Stock;
use App\Models\StockMovimiento;
use Carbon\Carbon;
use http\Exception;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class ComercioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(["Comercios"=>ComercioResource::collection(Comercio::all()),
        'message'=>"OK"],200);
    }

    public function show ($id)
    {

        $comercio = Comercio::find($id);

        if ($comercio!=null)
        {
            return response()->json(["Comercio"=> new ComercioResource($comercio), 'message'=>"OK"], 200);
        }
        else
        {
            return response()->json( ['message'=>"Comercio inexistente"], 200);
        }

    }

    /**
     * Devuelve los movimientos pendientes de liquidar de un comercio
     *
     * @param Request $request --> debe tener: "fecha" como raw data y el token del comercio
     * @return \Illuminate\Http\Response
     */
    public function consumosPendientesDeRendir (Request $request)
    {
        //por las dudas que venga mal formado el request
        try {
            $data=AdminGeneralController::devolverArrayDeRequestRawData($request);
        }
        catch (Exception $e)
        {
            return response()->json(['message'=>"ERROR - ".$e->getMessage()],500);
        }

        $fecha=Carbon::parse($data["fecha"]);
        $usuario= auth('sanctum')->user() ;

        $comercio=Comercio::devolverComercioxCuit($usuario->email);

        if ($comercio ==null)
        {
            return response()->json(['message'=>"Comercio inexistente"],401);
        }

        try {
            $consumos=Comercio::devolverConsumosPendientesDeLiquidar($fecha, $comercio->id);
            return response()->json(["Consumos"=>StockMovimientoResource::collection($consumos ),
                'message'=>"OK"],200);
        }
        catch(Exception $e)
        {
            return response()->json(['message'=>"ERROR - ".$e->getMessage()],500);
        }
    }

    /**
     * Devuelve los movimientos pendientes de liquidar de un comercio
     *
     * @param Request $request --> debe tener: "fecha" como raw data y el token del comercio
     * @return \Illuminate\Http\Response
     */
    public function consumir(Request $request)
    {
        $data=AdminGeneralController::devolverArrayDeRequestRawData($request);

        $fecha= Carbon::now();
        $persona=Persona::devolverPersonaxId($data["personaId"]);
        $articulo=Articulo::devolverArticuloxId($data["articuloId"]);

        $usuario= auth('sanctum')->user() ;
        $comercio=Comercio::devolverComercioxCuit($usuario->email);

        $stock=Stock::devolverStock( $data["personaId"], $fecha, $data["articuloId"]);

        $cantidad=(int) $data["cantidad"];

        if ($stock ==null)
        {
            return response()->json(['message'=>"No existe stock disponible para esa persona y artículo"], 200);
        }
        else
        {
            if ($stock->saldo >=$cantidad)
            {
                $consumoOK=StockMovimiento::Consumir($persona, $articulo, $fecha, $cantidad, $comercio,"Consumo via APP", $usuario, $stock);
                if ($consumoOK)
                    return response()->json(['message'=>"Consumo Registrado"], 200);
                else
                    return response()->json(['message'=>"Error: "], 400);

            }
            else
            {
                return response()->json(['message'=>"Stock insuficiente. Stock actual: ".$stock->cantidad], 200);

            }
        }
    }

    /**
     * Genera un nuevo cierre de lote con los consumos pendientes
     *
     * @param Request $request --> debe tener: ["observaciones",
     *                                          "movimientos:["movimiento_id"]"] como raw data y el token del comercio
     * @return \Illuminate\Http\Response
     */
    public function cerrarLote(Request $request)
    {
        //por las dudas que venga mal formado el request
        try {
            $data=AdminGeneralController::devolverArrayDeRequestRawData($request);
        }
        catch (Exception $e)
        {
            return response()->json(['message'=>"ERROR - ".$e->getMessage()],500);
        }

        $observaciones=$data["observaciones"];
        $usuario= auth('sanctum')->user() ;
        $comercio=Comercio::devolverComercioxCuit($usuario->email);
        $movimientos=$data["movimientos"];

        if ($comercio ==null)
        {
            return response()->json(['message'=>"Comercio inexistente"],401);
        }

        try {

            $ok=$comercio->cerrarLote($observaciones,$movimientos, $usuario);

            return response()->json(["lote"=>"OK",
                'message'=>"OK"],200);
        }
        catch(Exception $e)
        {
            return response()->json(['message'=>"ERROR - ".$e->getMessage()],500);
        }

    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comercio  $comercio
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comercio $comercio)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comercio  $comercio
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comercio $comercio)
    {
        //
    }
}
