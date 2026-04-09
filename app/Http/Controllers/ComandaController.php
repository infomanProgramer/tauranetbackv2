<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use PDF;
use DB;

class ComandaController extends ApiController
{
    public function queryComanda($id_venta_producto){
        return DB::table('venta_productos as vp')
            ->select(
                'vp.id_venta_producto',
                'r.nombre as nombre_restaurante',
                'r.tipo_moneda as tipo_moneda',
                's.nombre as nombre_sucursal',
                'c.nombre as nombre_caja',
                'vp.nro_venta as nro_pedido',
                'c2.nombre_completo as nombre_cliente',
                'dd.descripcion as tipo_servicio',
                DB::raw('DATE(vp.created_at) as fecha'),
                DB::raw("to_char(vp.created_at, 'HH24:MI') as hora"),
                'pv.cantidad',
                'pv.p_unit',
                'pv.importe',
                'p.nombre as nombre_producto',
                'pv.nota',
                'pa.importe as total',
                'pa.efectivo',
                'pa.cambio',
	            'pa.tipo_pago as cod_tipo_pago',
	            'pa.tipo_servicio as cod_tipo_servicio'
            )
            ->join('pagos as pa', 'pa.id_venta_producto', '=', 'vp.id_venta_producto')

            ->join('diccionario_datos as dd', function ($join) {
                $join->on('dd.codigo', '=', 'pa.tipo_servicio')
                    ->where('dd.tabla', 'pagos')
                    ->where('dd.campo', 'tipo_servicio');
            })
            ->leftJoin('clientes as c2', 'c2.id_cliente', '=', 'vp.id_cliente')
            ->join('historial_caja as hc', 'hc.id_historial_caja', '=', 'vp.id_historial_caja')
            ->join('cajas as c', 'c.id_caja', '=', 'hc.id_caja')
            ->join('sucursals as s', 's.id_sucursal', '=', 'c.id_sucursal')
            ->join('restaurants as r', 'r.id_restaurant', '=', 's.id_restaurant')
            ->join('producto_vendidos as pv', 'pv.id_venta_producto', '=', 'vp.id_venta_producto')
            ->join('productos as p', 'p.id_producto', '=', 'pv.id_producto')
            ->where('vp.id_venta_producto', $id_venta_producto)
            ->orderBy('pv.id_producto_vendido', 'asc')
            ->get();
    }

    public function verComanda(Request $request)
    {

        $result = $this->queryComanda($request->input('id_venta_producto'));
        if ($result->count() == 0) {
            $nro_pedido = 0;
            $fechaActual = date('d/m/Y');
        } else {
            $nro_pedido = $result[0]->nro_pedido;
            $fechaActual = $result[0]->fecha;
        }
        $isForCustomer = $request->input('isForCustomer');

        $data = [
            'result' => $result
        ];

        if($isForCustomer){
            $pdf = PDF::loadView('comandas.comandaCliente', $data);
            return $pdf->download('comandaCliente'.$fechaActual.'-'.$nro_pedido.'.pdf');
        }else{
            $pdf = PDF::loadView('comandas.comandaCocina', $data);
            return $pdf->download('comandaCocina'.$fechaActual.'-'.$nro_pedido.'.pdf');
        }
        
    }
}

