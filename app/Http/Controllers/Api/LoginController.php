<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login (Request $request)
    {
        $content = $request->getContent();

       $data= json_decode($content);
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

    public function validateLogin(Request $request)
    {

        return true;
//        return $request->validate([
//            'login'=>'required',
//            'password'=>'required'
//        ]);
    }
}
