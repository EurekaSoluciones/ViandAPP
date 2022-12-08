<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AdminGeneralController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\StockController;
use App\Http\Resources\V1\ComercioResource;
use App\Http\Resources\v1\StockMovimientoResource;
use App\Http\Resources\v1\PedidoGrupalResource;
use App\Models\Articulo;
use App\Models\Comercio;
use App\Models\PedidoGrupal;
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

        if (isset($data["qr"]))
            $persona=Persona::devolverPersonaxQR($data["qr"]);

        if (isset($data["personaId"]))
            $persona=Persona::devolverPersonaxId($data["personaId"]);

        if ($persona ==null)
        {
            return response()->json(['message'=>"No existe la persona"], 400);
        }

        $articulo=Articulo::devolverArticuloxId($data["articuloId"]);

        if ($articulo ==null)
        {
            return response()->json(['message'=>"No existe el artículo"], 400);
        }

        $usuario= auth('sanctum')->user() ;
        $comercio=Comercio::devolverComercioxCuit($usuario->email);

        $stock=Stock::devolverStock( $persona->id, $fecha, $articulo->id);

        $cantidad=(int) $data["cantidad"];

        if ($stock ==null)
        {
            return response()->json(["exitoso"=>false, "consumo"=>null, 'message'=>"Error: No existe Disponible para esta persona"], 200);
        }
        else
        {
            if ($stock->saldo >=$cantidad)
            {
                $consumoOK=StockMovimiento::Consumir($persona, $articulo, $fecha, $cantidad, $comercio,"Consumo via APP", $usuario, $stock, true);
                if ($consumoOK["exitoso"])
                    return response()->json(["exitoso"=>$consumoOK["exitoso"], "consumo"=>new StockMovimientoResource($consumoOK["movimiento"]), 'message'=>"Consumo Registrado"], 200);
                else
                    return response()->json(["exitoso"=>$consumoOK["exitoso"], "consumo"=>null, 'message'=>"Error: ".$consumoOK["error"]], 200);

            }
            else
            {
                return response()->json(["exitoso"=>false, "consumo"=>null, 'message'=>"Disponible insuficiente para ese artículo."], 200);

            }
        }
    }

    /**
     * Devuelve los movimientos pendientes de liquidar de un comercio
     *
     * @param Request $request --> debe tener: "fecha" como raw data y el token del comercio
     * @return \Illuminate\Http\Response
     */
    public function anularConsumo(Request $request)
    {
        $data=AdminGeneralController::devolverArrayDeRequestRawData($request);

        $fecha= Carbon::now();
        $consumo=StockMovimiento::devolverStockMovimientoNoAnuladoxId($data["consumoId"]);

        $usuario= auth('sanctum')->user() ;
        $comercio=Comercio::devolverComercioxCuit($usuario->email);
        $observaciones=$data["motivo"];

        if ($consumo ==null)
        {
            return response()->json(['message'=>"No existe el consumo a anular"], 400);
        }
        else
        {
            $anulacionOK=StockMovimiento::AnularConsumo($consumo, $usuario, $observaciones);
            if ($anulacionOK["exitoso"])
                return response()->json(['message'=>"Consumo Anulado"], 200);
            else
                return response()->json(['message'=>"Error: ".$anulacionOK["error"]], 400);

        }
    }


    /**
     * Genera un nuevo cierre de lote con los consumos pendientes
     *
     * @param Request $request --> debe tener: ["observaciones",
     *                                          "fecha:"
     *                                                  como raw data y el token del comercio
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
        $fecha=$data["fecha"];

        $comercio=Comercio::devolverComercioxCuit($usuario->email);

        if ($comercio ==null)
        {
            return response()->json(['message'=>"Comercio inexistente"],401);
        }

        try {

            $resultado=$comercio->cerrarLote($observaciones,$fecha, $usuario);

            if ($resultado["exitoso"]==true)
                return response()->json(["lote"=>$resultado["nroLote"], 'message'=>"OK"],200);
            else
                return response()->json(['message'=>"ERROR - ".$resultado["error"]],400);
        }
        catch(Exception $e)
        {
            return response()->json(['message'=>"ERROR - ".$e->getMessage()],500);
        }

    }

    /**
     * Devuelve los pedidos grupales realizados por los administradores
     *
     * @param Request $request --> no tiene parametros. Solo token de autorizacion del comercio
     * @return \Illuminate\Http\Response
     */
    public function pedidosGrupales (Request $request)
    {

        $usuario= auth('sanctum')->user() ;

        $comercio=Comercio::devolverComercioxCuit($usuario->email);

        if ($comercio ==null)
        {
            return response()->json(['message'=>"Comercio inexistente"],401);
        }

        try {
            $pedidos=PedidoGrupal::devolverPedidosgrupales( $comercio);

            return response()->json(["cantidadPedidos"=>count($pedidos),
                "Pedidos"=>PedidoGrupalResource::collection($pedidos),
                'message'=>"OK"],200);
        }
        catch(Exception $e)
        {
            return response()->json(['message'=>"ERROR - ".$e->getMessage()],500);
        }
    }

    /**
     * Aprueba un pedido grupal realizado por un administrador
     *
     * @param Request $request --> debe tener: ["pedidoId",
     *                                          "observaciones"] como raw data y el token del comercio
     * @return \Illuminate\Http\Response
     */
    public function confirmarPedidoGrupal(Request $request)
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
        $idPedido=$data["pedidoId"];

        $comercio=Comercio::devolverComercioxCuit($usuario->email);

        if ($comercio ==null)
        {
            return response()->json(['message'=>"Comercio inexistente"],401);
        }

        try {

            $resultado=$comercio->confirmarPedidoGrupal($observaciones,$idPedido, $usuario);

            if ($resultado["exitoso"]==true)
                return response()->json(['message'=>"OK"],200);
            else
                return response()->json(['message'=>"ERROR - ".$resultado["error"]],400);
        }
        catch(Exception $e)
        {
            return response()->json(['message'=>"ERROR - ".$e->getMessage()],500);
        }

    }


    /**
     * Aprueba un pedido grupal realizado por un administrador
     *
     * @param Request $request --> debe tener: ["pedidoId",
     *                                          "observaciones"] como raw data y el token del comercio
     * @return \Illuminate\Http\Response
     */
    public function rechazarPedidoGrupal(Request $request)
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
        $idPedido=$data["pedidoId"];

        $comercio=Comercio::devolverComercioxCuit($usuario->email);

        if ($comercio ==null)
        {
            return response()->json(['message'=>"Comercio inexistente"],401);
        }

        try {

            $resultado=$comercio->rechazarPedidoGrupal($observaciones,$idPedido, $usuario);

            if ($resultado["exitoso"]==true)
                return response()->json(['message'=>"OK"],200);
            else
                return response()->json(['message'=>"ERROR - ".$resultado["error"]],400);
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
