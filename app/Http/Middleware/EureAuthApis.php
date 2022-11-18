<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EureAuthApis
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $usuario= auth('sanctum')->user() ;

        if ($usuario==null)
        {
            return response()->json([
                'message'=>'Usuario no encontrado o clave no válida'
            ], 401);
        }

        return $next($request);
    }
}
