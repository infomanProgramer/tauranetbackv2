<?php

namespace App\Http\Controllers;

use App\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use DB;
use Validator;
use Illuminate\Support\Facades\Log;

class SucursalController extends ApiController
{
//    public function __construct()
//    {
//        $this->middleware('auth:mozo,admin');
//    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //Obtiene las sucursales por restaurante
    public function index($pag, $act)
    {
        //Si $act = 1 devuelve todas las sucursales activas
        //Si $act = 0 devuelve todas las sucursales activas e inactivas
        if($act == 1){//Muestra solo restaurantes activos
            $restaurant = DB::table('sucursals as s')
                ->where('s.estado', '=', 'true')
                ->select('*')
                ->orderBy('r.created_at', 'desc')
                ->paginate($pag);
            $response = Response::json(['data' => $restaurant], 200);
            return $response;
        }
        else{//Muestra todos los restaurantes
            $restaurant = DB::table('restaurants as r')
                ->join('superadministradors as s', 'r.id_superadministrador', '=', 's.id_superadministrador')
                ->select('r.*', 's.nombre_usuario')
                ->orderBy('r.created_at', 'desc')
                ->paginate($pag);
            $response = Response::json(['data' => $restaurant], 200);
            return $response;
        }
    }
    // public function sucursalPorRestaurante($pag, $id){
    //     $restaurant = DB::table('sucursals as s')
    //         ->join('restaurants as r', function($join){
    //             $join->on( 'r.id_restaurant', '=', 's.id_restaurant');
    //         })
    //         ->where('s.id_restaurant', '=', $id)
    //         ->select('s.*', 'r.nombre as nombreRestaurante')
    //         ->orderBy('s.created_at', 'desc')
    //         ->paginate($pag);
    //     $response = Response::json(['data' => $restaurant], 200);
    //     return $response;
    // }
    public function sucursalPorRestaurante($pag){
        $restaurant = DB::table('sucursals as s')
            ->join('restaurants as r', function($join){
                $join->on( 'r.id_restaurant', '=', 's.id_restaurant');
            })
            ->select('s.*', 'r.nombre as nombreRestaurante')
            ->orderBy('s.created_at', 'desc')
            ->paginate($pag);
        $response = Response::json(['data' => $restaurant], 200);
        return $response;
    }
    //Obtiene todas las sucursales, habilitado solo para el administrador
    public function todasSucursales($pag){
        $restaurant = DB::table('sucursals as s')
            ->join('restaurants as r', 'r.id_restaurant', '=', 's.id_restaurant')
            ->select('s.*', 'r.nombre as nombreRestaurante')
            ->orderBy('s.created_at', 'desc')
            ->paginate($pag);
        $response = Response::json(['data' => $restaurant], 200);
        return $response;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info("Entrando a store guardar sucursal");
        Log::info($request->all());
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|min:4|max:50|regex:/^[a-zA-Z0-9\s]+$/',
            'descripcion' => 'min:5|nullable',
            // 'direccion' => 'required|min:5|nullable',
            // 'telefono' => 'required_without_all:celular',
            // 'celular' => 'required_without_all:telefono',
            // 'ciudad' => 'required|min:3|max:20|nullable',
            // 'pais' => 'required|min:3|max:20|nullable',
            'id_superadministrador' => 'required|exists:superadministradors,id_superadministrador',
            'id_restaurant' => 'required|not_in:-1|exists:restaurants,id_restaurant'
        ],
        $messages = [
            'nombre.required' => 'El Nombre es requerido',
            'nombre.unique' => 'El Nombre ya existe',
            'nombre.min' => 'El Nombre tiene que tener 4 caracteres como mínimo',
            'nombre.max' => 'El Nombre tiene que tener 50 caracteres como maximo',
            'nombre.regex' => 'El Nombre tiene que tener solo caracteres y números',
            'descripcion.min' => 'La Descripción tiene que tener 4 caracteres como mínimo',
            'direccion.required' => 'La Dirección es requerida',
            'direccion.min' => 'La Dirección tiene que tener 4 caracteres como mínimo',
            'telefono.required_without_all' => 'El Telefono o Celular es requerido',
            'telefono.min' => 'El Telefono tiene que tener 4 digitos como mínimo',
            'telefono.max' => 'El Telefono tiene que tener 10 digitos como maximo',
            'telefono.numeric' => 'El Telefono tiene que ser de tipo númerico',
            'celular.required_without_all' => 'El Telefono o Celular es requerido',
            'celular.min' => 'El Celular tiene que tener 4 digitos como mínimo',
            'celular.max' => 'El Celular tiene que tener 10 digitos como maximo',
            'celular.numeric' => 'El Celular tiene que ser de tipo númerico',
            'pais.required' => 'El País es requerido',
            'pais.min' => 'El País tiene que tener 4 caracteres como mínimo',
            'pais.max' => 'El Celular tiene que tener 10 caracteres como maximo',
            'ciudad.required' => 'La Ciudad es requerida',
            'ciudad.min' => 'La Ciudad tiene que tener 4 caracteres como mínimo',
            'ciudad.max' => 'La Ciudad tiene que tener 10 caracteres como maximo',
            'id_superadministrador.required' => 'El Super Admin es requerido',
            'id_superadministrador.exists' => 'El Super Admin no existe',
            'id_restaurant.required' => 'El Restaurante es requerido',
            'id_restaurant.not_in' => 'El Restaurante es requerido',
            'id_restaurant.exists' => 'El Restaurante no existe',
        ]);
        $validator->sometimes('telefono', 'nullable|min:4|max:10|digits_between:0,9', function($input){
            return ! empty( $input->telefono );
        });
        $validator->sometimes('celular', 'nullable|min:4|max:10|digits_between:0,9', function($input){
            return ! empty( $input->celular );
        });
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 201);
        }
        $sucursal = new Sucursal();
        $sucursal->nombre = $request->get("nombre");
        $sucursal->descripcion = $request->get("descripcion");
        // $sucursal->direccion = $request->get("direccion");
        // $sucursal->celular = $request->get("celular");
        // $sucursal->telefono = $request->get("telefono");
        // $sucursal->pais = $request->get("pais");
        // $sucursal->ciudad = $request->get("ciudad");
        $sucursal->id_superadministrador = $request->get("id_superadministrador");
        $sucursal->id_restaurant = $request->get("id_restaurant");
        $sucursal->save();
        return response()->json(['data' => $sucursal], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sucursal = Sucursal::find($id);
        return response()->json(['data' => $sucursal], 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $sucursal = Sucursal::find($id);
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|min:4|max:50|regex:/^[a-zA-Z0-9\s]+$/',
            'descripcion' => 'min:5|nullable',
            // 'direccion' => 'required|min:5|nullable',
            // 'telefono' => 'required_without_all:celular',
            // 'celular' => 'required_without_all:telefono',
            // 'ciudad' => 'required|min:3|max:20|nullable',
            // 'pais' => 'required|min:3|max:20|nullable',
            'id_superadministrador' => 'required|exists:superadministradors,id_superadministrador',
            'id_restaurant' => 'required|not_in:-1|exists:restaurants,id_restaurant'

        ],
        $messages = [
            'nombre.required' => 'El Nombre es requerido',
            'nombre.unique' => 'El Nombre ya existe',
            'nombre.min' => 'El Nombre tiene que tener 4 caracteres como mínimo',
            'nombre.max' => 'El Nombre tiene que tener 50 caracteres como maximo',
            'nombre.regex' => 'El Nombre tiene que tener solo caracteres y números',
            'estado.required' => 'El Estado es requerido',
            'estado.boolean' => 'El Estado debe ser booleano',
            'descripcion.min' => 'La Descripción tiene que tener 4 caracteres como mínimo',
            'direccion.required' => 'La Dirección es requerida',
            'direccion.min' => 'La Dirección tiene que tener 4 caracteres como mínimo',
            'telefono.required_without_all' => 'El Telefono o Celular es requerido',
            'telefono.min' => 'El Telefono tiene que tener 4 digitos como mínimo',
            'telefono.max' => 'El Telefono tiene que tener 10 digitos como maximo',
            'telefono.numeric' => 'El Telefono tiene que ser de tipo númerico',
            'celular.required_without_all' => 'El Telefono o Celular es requerido',
            'celular.min' => 'El Celular tiene que tener 4 digitos como mínimo',
            'celular.max' => 'El Celular tiene que tener 10 digitos como maximo',
            'celular.numeric' => 'El Celular tiene que ser de tipo númerico',
            'pais.required' => 'El País es requerido',
            'pais.min' => 'El País tiene que tener 4 caracteres como mínimo',
            'pais.max' => 'El Celular tiene que tener 10 caracteres como maximo',
            'ciudad.required' => 'La Ciudad es requerida',
            'ciudad.min' => 'La Ciudad tiene que tener 4 caracteres como mínimo',
            'ciudad.max' => 'La Ciudad tiene que tener 10 caracteres como maximo',
            'id_superadministrador.required' => 'El Super Admin es requerido',
            'id_superadministrador.exists' => 'El Super Admin no existe',
            'id_restaurant.required' => 'El Restaurante es requerido',
            'id_restaurant.not_in' => 'El Restaurante es requerido',
            'id_restaurant.exists' => 'El Restaurante no existe'
        ]);
        $validator->sometimes('telefono', 'nullable|min:4|max:10|digits_between:0,9', function($input){
            return ! empty( $input->telefono );
        });
        $validator->sometimes('celular', 'nullable|min:4|max:10|digits_between:0,9', function($input){
            return ! empty( $input->celular );
        });
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 201);
        }
        if($request->has('nombre')){
            $sucursal->nombre = $request->nombre;
        }
        if($request->has('estado')){
            $sucursal->estado = $request->estado;
        }
        if($request->has('descripcion')){
            $sucursal->descripcion = $request->descripcion;
        }
        if($request->has('direccion')){
            $sucursal->direccion = $request->direccion;
        }
        if($request->has('celular')){
            $sucursal->celular = $request->celular;
        }
        if($request->has('telefono')){
            $sucursal->telefono = $request->telefono;
        }
        if($request->has('pais')){
            $sucursal->pais = $request->pais;
        }
        if($request->has('ciudad')){
            $sucursal->ciudad = $request->ciudad;
        }
        if($request->has('id_superadministrador')){
            $sucursal->id_superadministrador = $request->id_superadministrador;
        }
        if($request->has('id_restaurant')){
            $sucursal->id_restaurant = $request->id_restaurant;
        }
        if(!$sucursal->isDirty()){
            return $this->errorResponse(['valores' => 'Se debe modificar al menos un valor para poder actualizar'], 201);
        }
        $sucursal->save();
        return response()->json(['data' => $sucursal], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
