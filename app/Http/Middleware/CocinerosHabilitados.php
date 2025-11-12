<?php

namespace App\Http\Middleware;

use Closure;
use DB;

class CocinerosHabilitados
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
        }else {
            $suscripcion = DB::table('sucursals as s')
                ->join('restaurants as r', 'r.id_restaurant', '=', 's.id_restaurant')
                ->join('suscripcions as u', 'u.id_suscripcion', '=', 'r.id_suscripcion')
                ->join('plan_de_pagos as pl', 'pl.id_suscripcion', '=', 'u.id_suscripcion')
                ->where('s.id_sucursal', '=', $request->id_sucursal)
                ->select('pl.*')
                ->first();
            $nro_cocineros = DB::table('cocineros as n')
                ->where('n.id_sucursal', '=', $request->id_sucursal)
                ->whereNull('n.deleted_at')
                ->count();
            if ($nro_cocineros < $suscripcion->cant_cocineros) {
                return $next($request);
            } else {
                return response()->json(['error' => ['limite_cocineros' => 'No se puede habilitar mas de ' . $suscripcion->cant_cocineros . ' cocineros']], 201);
            }
        }
    }
}
