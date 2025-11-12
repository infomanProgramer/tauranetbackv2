<?php

namespace App\Http\Controllers;

use App\ProductoVendido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Validator;
use DB;

class ProductoVendidoController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($idVentaProducto)
    {
        $pvendidos = DB::table('producto_vendidos as p')
                        ->join('venta_productos as vp', 'vp.id_venta_producto', '=', 'p.id_venta_producto')
                        ->join('productos as r', 'r.id_producto', '=', 'p.id_producto')
                        ->where('p.id_venta_producto', '=', $idVentaProducto)
                        ->where('vp.estado_venta', '=', 0)
                        ->select('p.*', 'r.nombre as detalle')
                        ->get();
        $vproducto = DB::table('venta_productos as v')
                        ->leftJoin('clientes as c', 'c.id_cliente', '=', 'v.id_cliente')
                        ->where('v.id_venta_producto', '=', $idVentaProducto)
                        ->where('v.estado_venta', '=', 0)
                        ->select('v.*', DB::raw("CASE WHEN c.nombre_completo isNull THEN NULL ELSE c.nombre_completo END AS nombre_completo"), DB::raw("CASE WHEN c.dni isNull THEN NULL ELSE c.dni END as dni"))
                        ->get();
        $pago = DB::table('pagos as p')
                        ->join('venta_productos as vp', 'vp.id_venta_producto', '=', 'p.id_venta_producto')
                        ->where('p.id_venta_producto', '=', $idVentaProducto)
                        ->where('vp.estado_venta', '=', 0)
                        ->select('p.*')
                        ->get();
        $response = Response::json(['data' => $pvendidos, 'vprod' => $vproducto, 'vpag' => $pago], 200);
        return $response;
    }
}
