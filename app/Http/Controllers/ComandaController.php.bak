<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use PDF;

class ComandaController extends ApiController
{
    public function verComanda(Request $request)
    {
        \Log::info('metodo verComanda:');
        \Log::info('Request data:', $request->all());
        
        $nombre_restaurant = $request->input('nombre_restaurant');
        $sucursal = $request->input('sucursal');
        $caja = $request->input('caja');
        $nro_pedido = $request->input('nro_pedido');
        $listaProductos = $request->input('listaProductos');
        $datosCliente = $request->input('datosCliente');
        $paymentDetails = $request->input('paymentDetails');
        $identificacion = $request->input('identificacion');
        $isForCustomer = $request->input('isForCustomer');

        //Parsear lista de productos
        $listaProductos = explode(':', $listaProductos);
        $listaProductos = array_map(function ($item) {
            list($id_producto, $cantidad, $p_unit, $importe, $nota, $p_base, $importe_base, $detalle) = explode('|', $item);
            return (object) [
                'id_producto' => $id_producto,
                'cantidad' => $cantidad,
                'p_unit' => $p_unit,
                'importe' => $importe,
                'nota' => $nota,
                'p_base' => $p_base,
                'importe_base' => $importe_base,
                'detalle' => $detalle
            ];
        }, $listaProductos);
  
        //obtener fecha actual
        $fechaActual = date('d/m/Y');
        
        $data = [
            'numero' => $nro_pedido,
            'datosCliente' => $datosCliente,
            'items'  => $listaProductos,
            'paymentDetails' => $paymentDetails,
            'nombre_restaurant' => $nombre_restaurant,
            'sucursal' => $sucursal,
            'caja' => $caja,
            'fechaActual' => $fechaActual,
            'identificacion' => $identificacion
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

