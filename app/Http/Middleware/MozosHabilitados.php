<?php

namespace App\Http\Middleware;

use Closure;
use DB;

class MozosHabilitados
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
            $nro_mozos = DB::table('mozos as m')
                ->where('m.id_sucursal', '=', $request->id_sucursal)
                ->whereNull('m.deleted_at')
                ->count();
//            \Log::info('Cantidad de cajeros habilitados ' . $suscripcion->cant_cajeros);
//            \Log::info('Cajeros existentes ' . $nro_cajeros);
            if ($nro_mozos < $suscripcion->cant_mozos) {
                return $next($request);
            } else {
                return response()->json(['error' => ['limite_mozos' => 'No se puede habilitar mas de ' . $suscripcion->cant_mozos . ' mozos']], 201);
            }
        }
    }
}
