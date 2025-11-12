<?php

namespace App\Http\Controllers;

use App\Restaurant;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Response;
use Validator;

class RestaurantController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($pag, $act){
        if($act == 1){//Muestra solo restaurantes activos
            $restaurant = DB::table('restaurants as r')
                ->join('superadministradors as s', 'r.id_superadministrador', '=', 's.id_superadministrador')
                ->leftjoin('suscripcions as su', 'su.id_suscripcion', '=', 'r.id_suscripcion')
                ->where('r.estado', '=', 'true')
                ->select('r.*', 's.nombre_usuario', 'su.tipo_suscripcion')
                ->orderBy('r.created_at', 'desc')
                ->paginate($pag);
            $response = Response::json(['data' => $restaurant], 200);
            return $response;
        }
        else{//Muestra todos los restaurantes
            $restaurant = DB::table('restaurants as r')
                ->join('superadministradors as s', 'r.id_superadministrador', '=', 's.id_superadministrador')
                ->leftjoin('suscripcions as su', 'su.id_suscripcion', '=', 'r.id_suscripcion')
                ->select('r.*', 's.nombre_usuario', 'su.tipo_suscripcion')
                ->orderBy('r.created_at', 'desc')
                ->paginate($pag);
            $response = Response::json(['data' => $restaurant], 200);
            return $response;
        }
    }
    public function getallrestaurants(){
        $restaurant = DB::table('restaurants as r')
            ->select('*')
            ->where('r.estado', '=', 'true')
            ->orderBy('r.nombre', 'asc')
            ->get();
        $response = Response::json(['data' => $restaurant], 200);
        return $response;
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|min:4|max:50|regex:/^[a-zA-Z0-9\s]+$/|unique:restaurants',
            'tipo_moneda' => 'required|max:5|min:1',
            'identificacion' => 'required|max:10|min:1',
            'estado' => 'required|boolean',
            'id_superadministrador' => 'required|exists:superadministradors,id_superadministrador',
            'id_suscripcion' => 'required|not_in:-1|exists:suscripcions,id_suscripcion'
        ],
        $messages = [
            'nombre.required' => 'El Nombre es requerido',
            'nombre.unique' => 'El Nombre ya existe',
            'nombre.min' => 'El Nombre tiene que tener 4 caracteres como mínimo',
            'nombre.max' => 'El Nombre tiene que tener 50 caracteres como maximo',
            'nombre.regex' => 'El Nombre tiene que tener solo caracteres y números',
            'estado.required' => 'El Estado es requerido',
            'estado.boolean' => 'El Estado debe ser booleano',
            'id_superadministrador.required' => 'El Super Admin es requerido',
            'id_superadministrador.exists' => 'El Super Admin no existe',
            'id_suscripcion.required' => 'El tipo de suscripción es requerido',
            'id_suscripcion.exists' => 'El tipo de suscripción no existe',
            'id_suscripcion.not_in' => 'El tipo de suscripción es requerido'
        ]);
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 201);
        }
        $restaurant = new Restaurant();
        $restaurant->nombre = $request->get("nombre");
        $restaurant->estado = Restaurant::RESTAURANT_ACTIVO;
        $restaurant->descripcion = $request->get("descripcion");
        $restaurant->observacion = $request->get("observacion");
        $restaurant->id_superadministrador = $request->get("id_superadministrador");
        $restaurant->tipo_moneda = $request->get("tipo_moneda");
        $restaurant->identificacion = $request->get("identificacion");
        $restaurant->id_suscripcion = $request->get("id_suscripcion");
        $restaurant->save();
        return response()->json(['data' => $restaurant], 201);
    }
    public function show($id){
        $restaurant = Restaurant::find($id);
        return response()->json(['data' => $restaurant], 201);
    }
    public function update(Request $request, $id){
        $restaurant = Restaurant::find($id);
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|min:4|max:50|regex:/^[a-zA-Z0-9\s]+$/|unique:restaurants,nombre,'.$restaurant->id_restaurant.',id_restaurant',
            'estado' => 'required|boolean',
            'tipo_moneda' => 'required|max:5|min:1',
            'identificacion' => 'required|max:10|min:1',
            'id_superadministrador' => 'required|exists:superadministradors,id_superadministrador',
            'id_suscripcion' => 'required|not_in:-1|exists:suscripcions,id_suscripcion'
        ],
        $messages = [
            'nombre.required' => 'El Nombre es requerido',
            'nombre.unique' => 'El Nombre ya existe',
            'nombre.min' => 'El Nombre tiene que tener 4 caracteres como mínimo',
            'nombre.max' => 'El Nombre tiene que tener 50 caracteres como maximo',
            'nombre.regex' => 'El Nombre tiene que tener solo caracteres y números',
            'estado.required' => 'El Estado es requerido',
            'estado.boolean' => 'El Estado debe ser booleano',
            'descripcion.required' => 'La Descripción es requerido',
            'observacion.required' => 'La Observacion es requerido',
            'id_superadministrador.required' => 'El Super Admin es requerido',
            'id_superadministrador.exists' => 'El Super Admin no existe',
            'id_suscripcion.required' => 'El tipo de suscripción es requerido',
            'id_suscripcion.exists' => 'El tipo de suscripción no existe',
            'id_suscripcion.not_in' => 'El tipo de suscripción es requerido'
        ]);
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 201);
        }
        if($request->has('nombre')){
            $restaurant->nombre = $request->nombre;
        }
        if($request->has('estado')){
            $restaurant->estado = $request->estado;
        }
        if($request->has('descripcion')){
            $restaurant->descripcion = $request->descripcion;
        }
        if($request->has('observacion')){
            $restaurant->observacion = $request->observacion;
        }
        if($request->has('id_superadministrador')){
            $restaurant->id_superadministrador = $request->id_superadministrador;
        }
        if($request->has('id_suscripcion')){
            $restaurant->id_suscripcion = $request->id_suscripcion;
        }
        if($request->has('tipo_moneda')){
            $restaurant->tipo_moneda = $request->tipo_moneda;
        }
        if($request->has('identificacion')){
            $restaurant->identificacion = $request->identificacion;
        }
        if(!$restaurant->isDirty()){
            return $this->errorResponse(['valores' => 'Se debe modificar al menos un valor para poder actualizar'], 201);
        }
        $restaurant->save();
        $response = Response::json(['data' => $restaurant], 200);
        return $response;
    }
    public function updateMoneda(Request $request, $id){
        $restaurant = Restaurant::find($id);
        $validator = Validator::make($request->all(), [
            'tipo_moneda' => 'required|max:5|min:1',
        ],
            $messages = [
                'tipo_moneda.required' => 'El tipo de moneda es requerido',
                'tipo_moneda.min' => 'El tipo de moneda tiene que tener 1 caracter como mínimo',
                'tipo_moneda.max' => 'El tipo de moneda tiene que tener 5 caracteres como maximo'
            ]);
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 201);
        }
        if($request->has('tipo_moneda')){
            $restaurant->tipo_moneda = $request->tipo_moneda;
        }
        if(!$restaurant->isDirty()){
            return $this->errorResponse(['valores' => 'Se debe modificar al menos un valor para poder actualizar'], 201);
        }
        $restaurant->save();
        $response = Response::json(['data' => $restaurant], 200);
        return $response;
    }
    public function updateIdentificacion(Request $request, $id){
        $restaurant = Restaurant::find($id);
        $validator = Validator::make($request->all(), [
            'identificacion' => 'required|max:10|min:1',
        ],
            $messages = [
                'identificacion.required' => 'El tipo de identificación es requerido',
                'identificacion.min' => 'El tipo de identificación tiene que tener 1 caracter como mínimo',
                'identificacion.max' => 'El tipo de identificación tiene que tener 10 caracteres como maximo'
            ]);
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 201);
        }
        if($request->has('identificacion')){
            $restaurant->identificacion = $request->identificacion;
        }
        if(!$restaurant->isDirty()){
            return $this->errorResponse(['identificacion' => ['Se debe cambiar el valor para poder actualizar']], 201);
        }
        $restaurant->save();
        $response = Response::json(['data' => $restaurant], 200);
        return $response;
    }
    public function getDatosRestauranteSucursal($id, $type_user){
        if($type_user == 0){//Mozo
            $datosResSuc = DB::table('mozos as m')
                            ->join('sucursals as s', 's.id_sucursal', '=', 'm.id_sucursal')
                            ->join('restaurants as r', 'r.id_restaurant', '=', 's.id_restaurant')
                            ->select('s.nombre as sucursal', 'r.nombre as restaurant', 'r.tipo_moneda', 'r.identificacion', 's.ciudad', 's.pais', 's.direccion', 's.telefono', 's.celular')
                            ->where('m.id_mozo', '=', $id)
                            ->get();
            $response = Response::json(['data' => $datosResSuc], 200);
            return $response;
        }else if($type_user == 1){//Cajero
            $datosResSuc = DB::table('cajeros as c')
                ->join('cajas as j', 'j.id_caja', '=', 'c.id_caja')
                ->join('sucursals as s', 's.id_sucursal', '=', 'j.id_sucursal')
                ->join('restaurants as r', 'r.id_restaurant', '=', 's.id_restaurant')
                ->select('s.nombre as sucursal', 'r.nombre as restaurant', 'j.nombre as caja', 'r.tipo_moneda', 'r.identificacion', 's.ciudad', 's.pais', 's.direccion', 's.telefono', 's.celular')
                ->where('c.id_cajero', '=', $id)
                ->get();
            $response = Response::json(['data' => $datosResSuc], 200);
            return $response;

        }else if($type_user == 2) {//Administrador
            $datosResSuc = DB::table('administradors as a')
                ->join('restaurants as r', 'r.id_restaurant', '=', 'a.id_restaurant')
                ->select('r.nombre as restaurant', 'r.tipo_moneda', 'r.identificacion')
                ->where('a.id_administrador', '=', $id)
                ->get();
            $response = Response::json(['data' => $datosResSuc], 200);
            return $response;
        }else  if($type_user == 4){//Cocinero
            $datosResSuc = DB::table('cocineros as m')
                ->join('sucursals as s', 's.id_sucursal', '=', 'm.id_sucursal')
                ->join('restaurants as r', 'r.id_restaurant', '=', 's.id_restaurant')
                ->select('s.nombre as sucursal', 'r.nombre as restaurant', 'r.tipo_moneda', 'r.identificacion', 's.ciudad', 's.pais', 's.direccion', 's.telefono', 's.celular')
                ->where('m.id_cocinero', '=', $id)
                ->get();
            $response = Response::json(['data' => $datosResSuc], 200);
            return $response;
        }
    }
}
