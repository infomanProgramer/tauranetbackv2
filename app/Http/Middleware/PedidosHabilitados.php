<?php

namespace App\Http\Middleware;

use Closure;
use DB;

class PedidosHabilitados
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
        $pedidosPorDia = DB::table('venta_productos as v')
            ->join('historial_caja as h', 'h.id_historial_caja', '=', 'v.id_historial_caja')
            ->join('cajas as c', 'c.id_caja', '=', 'h.id_caja')
            ->join('sucursals as s', 's.id_sucursal', '=', 'c.id_sucursal')
            ->where(DB::raw('date(v.created_at)'), '=', DB::raw('current_date'))
            ->where('s.id_sucursal', '=', $request->id_sucursal)->count();
        //\Log::info('Pedidos por dia '.$pedidosPorDia);
        $suscripcion = DB::table('sucursals as s')
            ->join('restaurants as r', 'r.id_restaurant', '=', 's.id_restaurant')
            ->join('suscripcions as u', 'u.id_suscripcion', '=', 'r.id_suscripcion')
            ->join('plan_de_pagos as pl', 'pl.id_suscripcion', '=', 'u.id_suscripcion')
            ->where('s.id_sucursal', '=', $request->id_sucursal)
            ->select('pl.*')
            ->first();
        //\Log::info('pedidos habilitados '.$suscripcion->cant_pedidos);
        if($suscripcion->cant_pedidos == -1){
            return $next($request);
        }else{
            if($pedidosPorDia < $suscripcion->cant_pedidos){
                return $next($request);
            }else{
                return response()->json(['error' => ['limite_pedidos' => 'Sobrepaso el limite de '.$suscripcion->cant_pedidos.' pedidos habilitados']], 201);
            }
        }
    }
}
