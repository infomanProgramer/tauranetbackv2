<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use DB;
use Validator;
use Log;

class PersonalController extends ApiController
{
    public function getAllCajeros($id_restaurant, $id_sucursal)
    {
        if (!is_numeric($id_restaurant) || !is_numeric($id_sucursal)) {
            return $this->errorResponse('Los parámetros deben ser númericos', 400);
        }

        Log::info('id_restaurant: ' . $id_restaurant);
        Log::info('id_sucursal: ' . $id_sucursal);

        $query = DB::table('cajeros as c')
        ->join('cajas as ca', 'ca.id_caja', '=', 'c.id_caja')
        ->join('sucursals as s', 's.id_sucursal', '=', 'ca.id_sucursal')
        ->join('restaurants as r', 'r.id_restaurant', '=', 's.id_restaurant')
        ->select(
            'c.id_cajero as id_usuario',
            'r.nombre as nombre_restaurante',
            's.nombre as nombre_sucursal',
            'ca.nombre as nombre_caja',
            'c.nombre_usuario',
            DB::raw("concat(c.primer_nombre,' ', c.segundo_nombre,' ',c.paterno,' ',c.materno) as nombre_completo"),
            'c.estado'
        )
        ->when($id_restaurant != -1, function ($query) use ($id_restaurant) {
            $query->where('r.id_restaurant', '=', $id_restaurant);
        })
        ->when($id_sucursal != -1, function ($query) use ($id_sucursal) {
            $query->where('s.id_sucursal', '=', $id_sucursal);
        })
        ->whereNull('c.deleted_at')
        ->orderBy('r.nombre')
        ->orderBy('s.nombre')
        ->orderBy('ca.nombre');
        
        $cajeros = $query->paginate(6);
        //Log::info($query->toSql());    
        $response = Response::json(['data' => $cajeros], 200);
        return $response;
    }
    public function filtra_personal($idRestaurant, $pag, $idSucursal){
        if(($idSucursal == -1)){
            $personal = DB::table(DB::raw("function_personal(".$idRestaurant.")"))
                ->paginate($pag);
            $response = Response::json(['data' => $personal], 200);
            return $response;
        }else if(($idSucursal != -1)){
            $personal = DB::table(DB::raw("function_personal(".$idRestaurant.")"))
                ->where('id_sucursal', '=', $idSucursal)
                ->paginate($pag);
            $response = Response::json(['data' => $personal], 200);
            return $response;
        }
    }
     public function filtra_personal_all($idRestaurant, $idSucursal, $perfil){
        if(($idSucursal == -1) && ($perfil == -1)){
            $personal = DB::table(DB::raw("function_personal(".$idRestaurant.")"))
                ->get();
            $response = Response::json(['data' => $personal], 200);
            return $response;
        }else if(($idSucursal == -1) && ($perfil != -1)){
            $personal = DB::table(DB::raw("function_personal(".$idRestaurant.")"))
                ->where('tipo_usuario', '=', $perfil)
                ->get();
            $response = Response::json(['data' => $personal], 200);
            return $response;
        }else if(($idSucursal != -1) && ($perfil == -1)){
            $personal = DB::table(DB::raw("function_personal(".$idRestaurant.")"))
                ->where('id_sucursal', '=', $idSucursal)
                ->get();
            $response = Response::json(['data' => $personal], 200);
            return $response;
        }else if(($idSucursal != -1) && ($perfil != -1)){
            $personal = DB::table(DB::raw("function_personal(".$idRestaurant.")"))
                ->where('id_sucursal', '=', $idSucursal)
                ->where('tipo_usuario', '=', $perfil)
                ->get();
            $response = Response::json(['data' => $personal], 200);
            return $response;
        }
    }
}
