<?php

namespace App\Http\Middleware;

use Closure;
use DB;

class CajasHabilitadas
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
        if($request->id_sucursal == -1) {
            return $next($request);
        }else{
            $nro_cajas = DB::table('cajas as c')
                ->whereNull('c.deleted_at')
                ->where('c.id_sucursal', '=', $request->id_sucursal)
                ->count();

            $suscripcion = DB::table('sucursals as s')
                ->join('restaurants as r', 'r.id_restaurant', '=', 's.id_restaurant')
                ->join('suscripcions as u', 'u.id_suscripcion', '=', 'r.id_suscripcion')
                ->join('plan_de_pagos as pl', 'pl.id_suscripcion', '=', 'u.id_suscripcion')
                ->where('s.id_sucursal', '=', $request->id_sucursal)
                ->select('pl.*')
                ->first();
            if ($nro_cajas < $suscripcion->cant_cajas) {
                return $next($request);
            } else {
                return response()->json(['error' => ['limite_cajas' => 'No se puede habilitar mas cajas']], 201);
            }
        }
    }
}
