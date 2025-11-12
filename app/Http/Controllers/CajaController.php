<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Caja;
use DB;
use Validator;

class CajaController extends ApiController
{
    public function index($pag)
    {
        $caja = DB::table('cajas as c')
            ->join('sucursals as s', 's.id_sucursal', '=', 'c.id_sucursal')
            ->join('restaurants as r', 'r.id_restaurant', '=', 's.id_restaurant')
            ->select('c.id_caja', 'c.nombre', 's.nombre as nombreSucursal', 'r.nombre as nombreRestaurante')
            ->whereNull('c.deleted_at')
            ->orderBy('c.created_at', 'desc')
            ->paginate($pag);
        $response = Response::json(['data' => $caja], 200);
        return $response;
    }

    public function allcajas($idSucursal){
        $caja = DB::table('cajas')
                ->where('id_sucursal', '=', $idSucursal)
                ->whereNull('deleted_at')
                ->orderBy('nombre', 'asc')
                ->get();
        $response = Response::json(['data' => $caja], 200);
        return $response;
    }

    public function allallcajas(){
        $caja = DB::table('cajas')
                ->whereNull('deleted_at')
                ->orderBy('nombre', 'asc')
                ->get();
        $response = Response::json(['data' => $caja], 200);
        return $response;
    }

    public function sucursalPerRestaurant($idRestaurant){
        $caja = DB::table('sucursals as s')
            ->where('s.id_restaurant', '=', $idRestaurant)
            ->orderBy('s.nombre', 'asc')
            ->get();
        $response = Response::json(['data' => $caja], 200);
        return $response;
    }

    public function getAllSucursals(){
        $caja = DB::table('sucursals as s')
            ->orderBy('s.nombre', 'asc')
            ->get();
        $response = Response::json(['data' => $caja], 200);
        return $response;
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|min:4|max:50',
            'id_superadministrador' => 'required|exists:superadministradors,id_superadministrador',
            'id_sucursal' => 'required|not_in:-1|exists:sucursals,id_sucursal'
        ], 
        $messages = [
            'nombre.required' => 'El Nombre es requerido',
            'nombre.min' => 'El Nombre tiene que tener 4 caracteres como mínimo',
            'nombre.max' => 'El Nombre tiene que tener 50 caracteres como maximo',
            'id_superadministrador.required' => 'El Super Administrador es requerido',
            'id_superadministrador.exists' => 'El Super Administrador no existe',
            'id_sucursal.required' => 'La Sucursal es requerida',
            'id_sucursal.not_in' => 'La Sucursal es requerida',
            'id_sucursal.exists' => 'La Sucursal no existe',
        ]);
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 201);
        }
        $caja = new Caja();
        $caja->nombre = $request->get("nombre");
        $caja->descripcion = $request->get("descripcion");
        $caja->id_superadministrador = $request->get("id_superadministrador");
        $caja->id_sucursal = $request->get("id_sucursal");
        $caja->save();
        return response()->json(['data' => $caja], 201);
    }
    public function update(Request $request, $id){
        $caja = Caja::find($id);
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|min:4|max:50',
            'id_superadministrador' => 'required|exists:superadministradors,id_superadministrador',
            'id_sucursal' => 'required|not_in:-1|exists:sucursals,id_sucursal'
        ],
        $messages = [
            'nombre.required' => 'El Nombre es requerido',
            'nombre.min' => 'El Nombre tiene que tener 4 caracteres como mínimo',
            'nombre.max' => 'El Nombre tiene que tener 50 caracteres como maximo',
            'id_superadministrador.required' => 'El Super Administrador es requerido',
            'id_superadministrador.exists' => 'El Super Administrador no existe',
            'id_sucursal.required' => 'La Sucursal es requerida',
            'id_sucursal.not_in' => 'La Sucursal es requerida',
            'id_sucursal.exists' => 'La Sucursal no existe',
        ]);
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 201);
        }
        if($request->has('nombre')) {
            $caja->nombre = $request->nombre;
        }
        if($request->has('descripcion')) {
            $caja->descripcion = $request->descripcion;
        }
        if($request->has('id_superadministrador')) {
            $caja->id_superadministrador = $request->id_superadministrador;
        }
        if($request->has('id_sucursal')) {
            $caja->id_sucursal = $request->id_sucursal;
        }
        if(!$caja->isDirty()){
            return $this->errorResponse(['valores' => 'Se debe modificar al menos un valor para poder actualizar'], 201);
        }
        $caja->save();
        return response()->json(['data' => $caja], 201);
    }
    public function show($id){
        $caja = DB::table('cajas as c')
            ->select(
                'c.id_caja',
                'c.nombre',
                'c.descripcion',
                'c.id_superadministrador',
                'c.id_sucursal',
                DB::raw('(SELECT s.id_restaurant FROM sucursals s WHERE s.id_sucursal = c.id_sucursal) AS id_restaurant')
            )
            ->where('c.id_caja', '=', $id)
            ->first();
        return response()->json(['data' => $caja], 201);
    }
    public function destroy($id){
        //Verifica si tiene cajero asignados
        $var = DB::table('cajas as c')
                    ->join('cajeros as a', 'a.id_caja', '=', 'c.id_caja')
                    ->where('c.id_caja', '=', $id)
                    ->whereNull('a.deleted_at')
                    ->get();
        if(sizeof($var) == 0){
            $caja = Caja::find($id)->delete();
            return response()->json(['data' => $caja], 201);
        }else{
            $obj = ['error' => 'No se puede eliminar, existen cajeros asignados a la categoria'];
            return response()->json(['data' => $obj], 201);
        }
    }
}