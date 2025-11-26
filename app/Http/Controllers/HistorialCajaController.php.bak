<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\HistorialCaja;
use DB;
use Validator;

class HistorialCajaController extends ApiController
{
    public function index($idCaja, $pag){
        $historial = DB::table('historial_caja as h')
                        ->where('id_caja', '=', $idCaja)
                        ->orderBy('created_at', 'desc')
                        ->paginate($pag);
        $response = Response::json(['data' => $historial], 200);
        return $response;
    }
    private function _updateCashAmount($idHistorialCaja){
        $hcaja = DB::table('pagos as p')
                        ->join('venta_productos as v', 'v.id_venta_producto', '=', 'p.id_venta_producto')
                        ->where('v.id_historial_caja', '=', $idHistorialCaja)
                        ->where('v.estado_venta', '=', 0)
                        ->get();
        $suma = 0;
        foreach ($hcaja as $key => $value) {
            $suma += $value->importe;
        }
        $historial = HistorialCaja::find($idHistorialCaja);
        $historial->monto = $historial->monto_inicial + $suma;
        $historial->save();
        return $historial;
    }
    public function updateCashAmount($idHistorialCaja){
        $historial = $this->_updateCashAmount($idHistorialCaja);
        $response = Response::json(['data' => $historial], 200);
        return $response;
    }
    public function store(Request $request, $idCaja){
        //Validar que no se registre con la misma fecha
        //Validar que no haya otra caja abierta
        $fecha_valida = DB::table('historial_caja')->where('id_caja', '=', $idCaja)->where('fecha', '=', $request->fecha)->get();
        if(sizeof($fecha_valida)==0){
            $estado_valida = DB::table('historial_caja')->where('id_caja', '=', $idCaja)->where('estado', '=', 'true')->get();
            if(sizeof($estado_valida)==0) {
                $validator = Validator::make($request->all(), [
                    'monto_inicial' => 'required|numeric|between:0,99999999.99',
                ],
                    $messages = [
                        'monto_inicial.required' => 'El monto inicial es requerido',
                        'monto_inicial.numeric' => 'El monto inicial tiene que ser de tipo numérico',
                        'monto_inicial.between' => 'El monto inicial tiene que estar entre 0 y 99999999.99',
                    ]);
                if ($validator->fails()) {
                    return response()->json(["error" => $validator->errors()], 201);
                }
                
                $fecha_actual = date('Y-m-d');
                
                $historial = new HistorialCaja();
                $historial->monto_inicial = $request->get("monto_inicial");
                $historial->monto = $request->get("monto_inicial");
                $historial->estado = $request->get("estado");
                $historial->fecha =   $fecha_actual;
                if ($request->has('id_administrador')) {
                    $historial->id_administrador = $request->get("id_administrador");
                }
                if ($request->has('id_cajero')) {
                    $historial->id_cajero = $request->get("id_cajero");
                }
                $historial->id_caja = $request->get("id_caja");
                $historial->save();
                return response()->json(['data' => $historial], 201);
            }
            else{
                return $this->errorResponse(['valores' => 'Existe otra apertura de caja que no ha sido cerrada'], 201);
            }
        }else{
            return $this->errorResponse(['valores' => 'Ya existe una apertura de caja con la fecha '.$fecha_actual], 201);
        }

    }
    public function cashClosing(Request $request, $id){
        if($request->has('estado')) {
            $historial = $this->_updateCashAmount($id);
            $historial->estado = $request->estado;
            $historial->save();
            return response()->json(['data' => $historial], 201);
        }
    }
}
