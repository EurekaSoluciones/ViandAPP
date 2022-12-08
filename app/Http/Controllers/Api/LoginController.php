<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AdminGeneralController;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\PedidoGrupalResource;
use App\Http\Resources\v1\PersonaResource;
use App\Models\Comercio;
use App\Models\PedidoGrupal;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login (Request $request)
    {

        $credentials=$this->validate(request(),
            [
                'login'=> 'required|string',
                'password'=>'required|string'
            ]);

        $password=Hash::make($credentials['password']);
               $user=User:: where('email',$credentials['login'])
            // ->where('password',$credentials['password'])
            ->first();

        if ($user!=null)
        {
            return response()->json([
                'token'=>$user->createtoken($user->email)->plainTextToken,
                'perfil'=>$user->perfil_id,
                'message'=>'OK'
            ], 200);
        }

        return response()->json([
            'message'=>'Usuario no encontrado o clave no válida'
        ], 401);
    }

    public function loginComercio (Request $request)
    {
        $data=AdminGeneralController::devolverArrayDeRequestRawData($request);

        $validator = Validator::make($data, [
            'users.*.login' => 'required|string',
            'users.*.password' => 'required|string'
        ]);


        $password=Hash::make($data['password']);
        $user=User:: where('email',$data['login'])
            ->where('perfil_id', config('global.PERFIL_Comercio'))
            ->first();

        if ($user!=null)
        {

            if (Hash::check($data['password'], $user->password)) {
                $comercio = Comercio::devolverComercioxCuit($data['login']);

                $pedidos=PedidoGrupal::devolverPedidosgrupales($comercio);

                return response()->json([
                    'token' => $user->createtoken($user->email)->plainTextToken,
                    'perfil' => $user->perfil_id,
                    'comercio' => $comercio,
                    "cantidadPedidos"=>count($pedidos),
                    "Pedidos"=>PedidoGrupalResource::collection($pedidos),
                    'message' => 'OK'
                ], 200);
            }
            else
            {
                return response()->json([
                    'message' => 'Clave no válida'
                ], 401);
            }
        }

        return response()->json([
            'message'=>'Comercio no encontrado'
        ], 401);
    }
    public function loginPersona (Request $request)
    {
//        $content = $request->getContent();
//
//        $data =get_object_vars(json_decode($content));

        $data=AdminGeneralController::devolverArrayDeRequestRawData($request);

        $validator = Validator::make($data, [
            'users.*.login' => 'required|string',
            'users.*.password' => 'required|string'
        ]);


        $password=Hash::make($data['password']);
        $user=User:: where('email',$data['login'])
            ->where('perfil_id', config('global.PERFIL_Persona'))
            ->first();

        if ($user!=null)
        {

            if (Hash::check($data['password'], $user->password)) {
                $persona = Persona::devolverPersonaxDni($data['login']);

                return response()->json([
                    'token' => $user->createtoken($user->email)->plainTextToken,
                    'perfil' => $user->perfil_id,
                    'persona' => new PersonaResource($persona),
                    'message' => 'OK'
                ], 200);
            }
            else
            {
                return response()->json([
                    'message' => 'Clave no válida'
                ], 401);
            }
        }

        return response()->json([
            'message'=>'Persona no encontrada'
        ], 401);
    }

    public function validateLogin(Request $request)
    {

        return true;
//        return $request->validate([
//            'login'=>'required',
//            'password'=>'required'
//        ]);
    }
}
