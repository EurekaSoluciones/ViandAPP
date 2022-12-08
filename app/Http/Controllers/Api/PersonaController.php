<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\AdminGeneralController;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\PersonaResource;
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

    }}
