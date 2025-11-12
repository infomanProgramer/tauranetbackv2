<?php

namespace App\Http\Middleware;

use Closure;
use DB;

class RestaurantActivo
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
        \Log::info($request);
        if($request->type_user == 0){//Mozo
            $verifica = DB::table('mozos')->where('nombre_usuario', '=', $request->nombre_usuario)->get();
            if(sizeof($verifica)){
                $req = DB::table('mozos as m')
                            ->join('sucursals as s', 's.id_sucursal', '=', 'm.id_sucursal')
                            ->join('restaurants as r', 'r.id_restaurant', '=', 's.id_restaurant')
                            ->where('m.nombre_usuario', '=', $request->nombre_usuario)
                            ->select('r.id_restaurant', 'r.estado')->first();
                if($req->estado){
                    return $next($request);
                }else{
                    return response()->json(['error' => 'Restaurante inactivo'], 201);
                }
            }else{
                return response()->json(['error' => 'Nombre de usuario incorrecto'], 201);
            }
        }else if($request->type_user == 1){//Cajero
            $verifica = DB::table('cajeros')->where('nombre_usuario', '=', $request->nombre_usuario)->get();
            if(sizeof($verifica)>0){
                $req = DB::table('cajeros as c')
                    ->join('cajas as j', 'j.id_caja', '=', 'c.id_caja')
                    ->join('sucursals as s', 's.id_sucursal', '=', 'j.id_sucursal')
                    ->join('restaurants as r', 'r.id_restaurant', '=', 's.id_restaurant')
                    ->where('c.nombre_usuario', '=', $request->nombre_usuario)
                    ->select('r.id_restaurant', 'r.estado')->first();
                if($req->estado){
                    return $next($request);
                }else{
                    return response()->json(['error' => 'Restaurante inactivo'], 201);
                }
            }else{
                return response()->json(['error' => 'Nombre de usuario incorrecto'], 201);
            }

        }else if($request->type_user == 2){//Administrador
            $verifica = DB::table('administradors')->where('nombre_usuario', '=', $request->nombre_usuario)->get();
            if(sizeof($verifica)>0) {
                $req = DB::table('administradors')->where('nombre_usuario', '=', $request->nombre_usuario)->select('id_restaurant')->first();
                $req_activo = DB::table('restaurants')->where('id_restaurant', '=', $req->id_restaurant)->first();
                if ($req_activo->estado) {
                    return $next($request);
                } else {
                    return response()->json(['error' => 'Restaurante inactivo'], 201);
                }
            }else{
                return response()->json(['error' => 'Nombre de usuario incorrecto'], 201);
            }
        }else if($request->type_user == 4){//Cocinero
            \Log::info('Entra por cocinero');
            $verifica = DB::table('cocineros')->where('nombre_usuario', '=', $request->nombre_usuario)->get();
            if(sizeof($verifica)){
                $req = DB::table('cocineros as m')
                    ->join('sucursals as s', 's.id_sucursal', '=', 'm.id_sucursal')
                    ->join('restaurants as r', 'r.id_restaurant', '=', 's.id_restaurant')
                    ->where('m.nombre_usuario', '=', $request->nombre_usuario)
                    ->select('r.id_restaurant', 'r.estado')->first();
                if($req->estado){
                    \Log::info('Pasa');
                    return $next($request);
                }else{
                    return response()->json(['error' => 'Restaurante inactivo'], 201);
                }
            }else{
                return response()->json(['error' => 'Nombre de usuario incorrecto'], 201);
            }
        }
    }
}
