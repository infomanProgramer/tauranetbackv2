<?php

namespace App\Http\Middleware;

use Closure;
use DB;

class UsuariosActivos
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->type_user == 0){//Mozo
            $mozo = DB::table('mozos')->where('nombre_usuario', '=', $request->nombre_usuario)->first();
            if($mozo->estado){
                return $next($request);
            }else{
                return response()->json(['error' => 'Usuario inactivo'], 201);
            }
        }else if($request->type_user == 1){//Cajero
            $cajero = DB::table('cajeros')->where('nombre_usuario', '=', $request->nombre_usuario)->first();
            if($cajero->estado){
                return $next($request);
            }else{
                return response()->json(['error' => 'Usuario inactivo'], 201);
            }
        }else if($request->type_user == 4){//Cocinero
            $cocinero = DB::table('cocineros')->where('nombre_usuario', '=', $request->nombre_usuario)->first();
            if($cocinero->estado){
                return $next($request);
            }else{
                return response()->json(['error' => 'Usuario inactivo'], 201);
            }
        }
    }
}
