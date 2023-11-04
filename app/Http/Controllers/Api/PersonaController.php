<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\AdminGeneralController;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\NotificacionResource;
use App\Http\Resources\V1\PersonaResource;
use App\Http\Resources\V1\StockMovimientoResource;
use App\Models\Notificacion;
use App\Models\NotificacionPersona;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PersonaController extends Controller
{

    /**
     * Devuelve una persona a partir de su QR
     *
     * @var $request
     */
    public function devolverPersonaxQR (Request $request)
    {
       $data=AdminGeneralController::devolverArrayDeRequestRawData($request);

       $qr=$data['qr'];

       $persona=Persona::devolverPersonaxQR($qr);

        if ($persona!=null) {
            /*Veamos si está activo*/

            if ($persona->activo)
                return response()->json(["Persona"=> new PersonaResource($persona), 'message'=>"OK"], 200);
            else
                return response()->json([
                    'message'=>'Persona Dada de Baja'], 400);
        }
        else
            {
                return response()->json([
                    'message'=>'Persona no encontrada - QR inválido'], 400);
            }

    }

    public function devolverPersonaxToken (Request $request)
    {
        $usuario= auth('sanctum')->user() ;
        $persona=Persona::devolverPersonaxDni($usuario->email);

        if ($persona!=null) {
            /*Veamos si está activo*/

            if ($persona->activo)
                return response()->json(["Persona"=> new PersonaResource($persona), 'message'=>"OK"], 200);
            else
                return response()->json([
                    'message'=>'Persona Dada de Baja'], 400);
        }
        else
        {
            return response()->json([
                'message'=>'Persona no encontrada - QR inválido'], 400);
        }

    }

    /**
     * Genera un nuevo código QR para una persona
     * @var $request -> debe venir solo el Token
     */
    public function generarNuevoQr (Request $request)
    {
        $usuario= auth('sanctum')->user() ;
        $persona=Persona::devolverPersonaxDni($usuario->email);

        if ($persona!=null) {
            /*Veamos si está activo*/

            if ($persona->activo)
            {
                $persona->generarQR();
                return response()->json(["Persona"=> new PersonaResource($persona), 'message'=>"OK"], 200);
            }

            else
                return response()->json([
                    'message'=>'Persona Dada de Baja'], 400);
        }
        else
        {
            return response()->json([
                'message'=>'Persona no encontrada'], 400);
        }

    }

    /**
     * Genera un nuevo código QR para una persona
     * @var $request -> debe venir solo el Token
     */
    public function marcarNotificacionLeida (Request $request)
    {
        $data=AdminGeneralController::devolverArrayDeRequestRawData($request);
        $notificacionId=$data['notificacionId'];

        $usuario= auth('sanctum')->user() ;

        $persona=Persona::devolverPersonaxDni($usuario->email);

        if ($persona!=null) {

            $persona->confirmarLecturaNotificacion($notificacionId);

            return response()->json(['message' => "OK"], 200);
        }
        else
        {
            return response()->json([
                'message'=>'Persona no encontrada'], 400);
        }

    }


    /**
     * Devuelve las notificaciones de una persona
     *
     * @param Request $request --> no tiene parametros. Solo token de autorizacion del comercio
     * @return \Illuminate\Http\Response
     */
    public function notificaciones (Request $request)
    {

        $usuario= auth('sanctum')->user() ;

        $persona=Persona::devolverPersonaxDni($usuario->email);

        if ($persona!=null) {
            /*Veamos si está activo*/

            if ($persona->activo)
            {
                try {
                    $notificaciones=NotificacionPersona::dePersona($persona->id);
                    $notificacionesNoLeidas=NotificacionPersona::noLeidasDePersona($persona->id);


                    return response()->json(["noleidas"=>count($notificacionesNoLeidas),
                        "Notificaciones"=>NotificacionResource::collection($notificaciones),
                        'message'=>"OK"],200);
                }
                catch(Exception $e)
                {
                    return response()->json(['message'=>"ERROR - ".$e->getMessage()],500);
                }
            }

            else
                return response()->json([
                    'message'=>'Persona Dada de Baja'], 400);
        }
        else
        {
            return response()->json([
                'message'=>'Persona no encontrada'], 400);
        }



    }

    /**
     * Devuelve los consumos de los 2 ultimos meses de una persona
     *
     * @param Request $request --> no tiene parametros. Solo token de autorizacion del comercio
     * @return \Illuminate\Http\Response
     */
    public function misConsumos (Request $request)
    {

        $usuario= auth('sanctum')->user() ;

        $persona=Persona::devolverPersonaxDni($usuario->email);

        if ($persona!=null) {
            /*Veamos si está activo*/

            if ($persona->activo)
            {
                try {
                    $consumos=$persona->ultimosConsumos()->get();

                    return response()->json(["Consumos"=>StockMovimientoResource::collection($consumos ),
                        'message'=>"OK"],200);
                }
                catch(Exception $e)
                {
                    return response()->json(['message'=>"ERROR - ".$e->getMessage()],500);
                }
            }

            else
                return response()->json([
                    'message'=>'Persona Dada de Baja'], 400);
        }
        else
        {
            return response()->json([
                'message'=>'Persona no encontrada'], 400);
        }



    }

}
