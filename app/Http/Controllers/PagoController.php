<?php

namespace App\Http\Controllers;

use App\VentaProducto;
use Illuminate\Http\Request;
use Validator;
use App\Pago;
use Illuminate\Support\Facades\Response;

class PagoController extends ApiController
{
    public function store(Request $request){
        \Log::info($request);
        $validator = Validator::make($request->all(), [
            'efectivo' => 'required|numeric|between:0,99999999.99',
            'id_cajero' => 'required',
            'total' => 'required|numeric|between:0,99999999.99',
            'total_pagar' => 'required|numeric|between:0,99999999.99',
            'visa' => 'required|numeric|between:0,99999999.99',
            'mastercard' => 'required|numeric|between:0,99999999.99',
            'cambio' => 'required|numeric|between:0,99999999.99',
            'id_venta_producto' => 'required|exists:venta_productos,id_venta_producto'
        ],
        $messages = [
            'efectivo.required' => 'El efectivo es requerido',
            'efectivo.numeric' => 'El efectivo tiene que ser de tipo numérico',
            'efectivo.between' => 'El efectivo tiene que estar entre 0 y 999999999,99',

            'total.required' => 'El total es requerido',
            'total.numeric' => 'El total tiene que ser de tipo numérico',
            'total.between' => 'El total tiene que estar entre 0 y 999999999,99',

            'total_pagar.required' => 'El total a pagar es requerido',
            'total_pagar.numeric' => 'El total a pagar tiene que ser de tipo numérico',
            'total_pagar.between' => 'El total a pagar tiene que estar entre 0 y 999999999,99',

            'visa.required' => 'Visa es requerida',
            'visa.numeric' => 'Visa tiene que ser de tipo numérico',
            'visa.between' => 'Visa tiene que estar entre 0 y 999999999,99',

            'mastercard.required' => 'Mastercard es requerida',
            'mastercard.numeric' => 'Mastercard tiene que ser de tipo numérico',
            'mastercard.between' => 'Mastercard tiene que estar entre 0 y 999999999,99',

            'cambio.required' => 'El cambio es requerida',
            'cambio.numeric' => 'El cambio tiene que ser de tipo numérico',
            'cambio.between' => 'El cambio tiene que estar entre 0 y 999999999,99'
        ]);
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 201);
        }
        if($request->efectivo >= $request->total){
            if($request->total_pagar >= $request->total) {
                $vproducto = VentaProducto::find($request->id_venta_producto);
                $vproducto->estado_venta = 'P';
                $vproducto->save();
                $pago = new Pago();
                $pago->efectivo = $request->get("efectivo");
                $pago->total = $request->get("total");
                $pago->total_pagar = $request->get("total_pagar");
                $pago->visa = $request->get("visa");
                $pago->mastercard = $request->get("mastercard");
                $pago->cambio = $request->get("cambio");
                $pago->id_cajero = $request->get("id_cajero");
                $pago->id_venta_producto = $request->get("id_venta_producto");
                $pago->save();
                return response()->json(['data' => $pago], 201);
            }else{
                return $this->errorResponse(['total_pagar_mayor' => 'El total a pagar tiene que ser mayor o igual al total'], 201);
            }
        }else{
            return $this->errorResponse(['efectivo_mayor' => 'El efectivo tiene que ser mayor o igual al total'], 201);
        }

    }
}
