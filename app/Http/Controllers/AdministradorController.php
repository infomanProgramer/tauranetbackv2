<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use App\Administrador;
use App\Restaurant;
use App\Superadministrador;
use Validator;
//use App\Http\Requests\AdministradorRequest;
use DB;

class AdministradorController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($pag)
    {
        $administrador = DB::table('administradors as a')
            ->join('restaurants as r', 'r.id_restaurant', '=', 'a.id_restaurant')
            ->leftjoin('suscripcions as s', 's.id_suscripcion', '=', 'r.id_suscripcion')
            ->select('a.*', DB::raw("concat(a.primer_nombre,' ',a.segundo_nombre,' ',a.paterno,' ',a.materno) as nombre_completo"), 'r.nombre as nombreRestaurante', 'r.estado as estado_resturante','s.tipo_suscripcion')
            ->orderBy('a.created_at', 'desc')
            ->paginate($pag);
        $response = Response::json(['data' => $administrador]);
        return $response;
    }

    public function administradorPorRestaurante($pag, $id){
        $administrador = DB::table('administradors as a')
            ->join('restaurants as r', 'r.id_restaurant', '=', 'a.id_restaurant')
            ->leftjoin('suscripcions as s', 's.id_suscripcion', '=', 'r.id_suscripcion')
            ->select('a.*', DB::raw("concat(a.primer_nombre,' ',a.segundo_nombre,' ',a.paterno,' ',a.materno) as nombre_completo"), 'r.nombre as nombreRestaurante', 'r.estado as estado_resturante','s.tipo_suscripcion')
            ->where('r.id_restaurant', '=', $id)
            ->orderBy('a.created_at', 'desc')
            ->paginate($pag);
        $response = Response::json(['data' => $administrador]);
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
        //\Log::info($request->image);

        $localRules = [
            'id_superadministrador' => 'required|exists:superadministradors,id_superadministrador',
            'id_restaurant' => 'required|not_in:-1|exists:restaurants,id_restaurant'
        ];
        $locaMessages = [
            'id_superadministrador.required' => 'El Super Admin es requerido',
            'id_superadministrador.exists' => 'El Super Admin no existe',
            'id_restaurant.required' => 'El Restaurante es requerido',
            'id_restaurant.not_in' => 'El Restaurante es requerido',
            'id_restaurant.exists' => 'El Restaurante no existe',
        ];
        $allRules = (new UserRequest())->rules() + $localRules;
        $allMessages = (new UserRequest())->messages() + $locaMessages;
        $validator = Validator::make($request->all(), $allRules, $allMessages);

        $validator->sometimes('primer_nombre', 'min:4|max:100', function($input){
            return ! empty( $input->primer_nombre );
        });

        $validator->sometimes('segundo_nombre', 'min:4|max:100', function($input){
            return ! empty( $input->segundo_nombre );
        });

        $validator->sometimes('paterno', 'min:4|max:100', function($input){
            return ! empty( $input->paterno );
        });

        $validator->sometimes('materno', 'min:4|max:100', function($input){
            return ! empty( $input->materno );
        });

        $validator->sometimes('celular', 'min:4|max:20|digits_between:0,9', function($input){
            return ! empty( $input->celular );
        });

        $validator->sometimes('telefono', 'min:4|max:20|digits_between:0,9', function($input){
            return ! empty( $input->telefono );
        });

        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 201);
        }

        $administrador = new Administrador;
        $administrador->primer_nombre = $request->get("primer_nombre");
        $administrador->segundo_nombre = $request->get("segundo_nombre");
        $administrador->paterno = $request->get("paterno");
        $administrador->materno = $request->get("materno");
        //$administrador->email = $request->get("email");
        //$administrador->dni = $request->get("dni");
        //$administrador->sexo = $request->get("sexo");
        //$administrador->direccion = $request->get("direccion");
        $administrador->nombre_usuario = $request->get("nombre_usuario");
        $administrador->password = bcrypt($request->get("password"));
        //$administrador->fecha_nac = $request->get("fecha_nac");
        $administrador->tipo_usuario = $request->get("tipo_usuario");
        //$administrador->nombre_fotoperfil = $request->get("nombre_fotoperfil");
        //$administrador->celular = $request->get("celular");
        //$administrador->telefono = $request->get("telefono");
        $administrador->tipo_usuario = $request->get("tipo_usuario");
        //Campos correspondientes solo al Administrador
        $administrador->id_restaurant = $request->get("id_restaurant");
        $administrador->id_superadministrador = $request->get("id_superadministrador"); //Se tiene que insertar segun el usuario que lo este registrando

        $administrador->save();
        return response()->json(['data' => $administrador], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $administrador = Administrador::findOrFail($id);
        return response()->json(['data' => $administrador], 201);
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
        $administrador = Administrador::find($id);
        $localRules = [
            'id_superadministrador' => 'required|exists:superadministradors,id_superadministrador',
            'id_restaurant' => 'required|not_in:-1|exists:restaurants,id_restaurant'
        ];
        $locaMessages = [
            'id_superadministrador.required' => 'El Super Admin es requerido',
            'id_superadministrador.exists' => 'El Super Admin no existe',
            'id_restaurant.required' => 'El Restaurante es requerido',
            'id_restaurant.not_in' => 'El Restaurante es requerido',
            'id_restaurant.exists' => 'El Restaurante no existe',
        ];
        $allRules = (new UserRequest())->rulesUpdate($administrador->id_usuario) + $localRules;
        $allMessages = (new UserRequest())->messages() + $locaMessages;
        $validator = Validator::make($request->all(), $allRules, $allMessages);

        $validator->sometimes('primer_nombre', 'min:4|max:100', function($input){
            return ! empty( $input->primer_nombre );
        });

        $validator->sometimes('segundo_nombre', 'min:4|max:100', function($input){
            return ! empty( $input->segundo_nombre );
        });

        $validator->sometimes('paterno', 'min:4|max:100', function($input){
            return ! empty( $input->paterno );
        });

        $validator->sometimes('materno', 'min:4|max:100', function($input){
            return ! empty( $input->materno );
        });

        // $validator->sometimes('celular', 'min:4|max:20|digits_between:0,9', function($input){
        //     return ! empty( $input->celular );
        // });

        // $validator->sometimes('telefono', 'min:4|max:20|digits_between:0,9', function($input){
        //     return ! empty( $input->telefono );
        // });

        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 201);
        }

        if($request->has('primer_nombre')) {
            $administrador->primer_nombre = $request->primer_nombre;
        }
        if($request->has('segundo_nombre')){
            $administrador->segundo_nombre = $request->segundo_nombre;
        }
        if($request->has('paterno')){
            $administrador->paterno = $request->paterno;
        }
        if($request->has('materno')){
            $administrador->materno = $request->materno;
        }
        // if($request->has('email')){
        //     $administrador->email = $request->email;
        // }
        // if($request->has('dni')){
        //     $administrador->dni = $request->dni;
        // }
        // if($request->has('sexo')){
        //     $administrador->sexo = $request->sexo;
        // }
        // if($request->has('direccion')){
        //     $administrador->direccion = $request->direccion;
        //}
        if($request->has('nombre_usuario')){
            $administrador->nombre_usuario = $request->nombre_usuario;
        }
        // if($request->has('fecha_nac')){
        //     $administrador->fecha_nac = $request->fecha_nac;
        //}
        // if($request->has('celular')) {
        //     $administrador->celular = $request->celular;
        //}
        // if($request->has('telefono')) {
        //     $administrador->telefono = $request->telefono;
        //}
        if($request->has('tipo_usuario')) {
            $administrador->tipo_usuario = $request->tipo_usuario;
        }
        //Campos correspondientes solo al Administrador
        if($request->has('id_restaurant')) {
            $administrador->id_restaurant = $request->id_restaurant;
        }
        if($request->has('id_superadministrador')) {
            $administrador->id_superadministrador = $request->id_superadministrador; //Se tiene que insertar segun el usuario que lo este registrando
        }
        // if($request->has('nombre_fotoperfil')) {
        //     $administrador->nombre_fotoperfil = $request->nombre_fotoperfil;
        // }
        if(!$administrador->isDirty()){
            return $this->errorResponse(['valores' => 'Se debe modificar al menos un valor para poder actualizar'], 201);
        }
        $administrador->save();
        $response = Response::json(['data' => $administrador], 200);
        return $response;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $administrador = Administrador::findOrFail($id);
        $administrador->delete();
        return $this->showOne($administrador);
    }
}
