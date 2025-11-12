<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Cocinero;
use Validator;
use DB;


class CocineroController extends ApiController
{
    public function store(Request $request){
        $localRules = [
            'id_administrador' => 'required|exists:administradors,id_administrador',
            'id_sucursal' => 'required|not_in:-1|exists:sucursals,id_sucursal',
            'sueldo' => 'required|numeric|between:0,9999999.99',
        ];
        $locaMessages = [
            'id_administrador.required' => 'El Administrador es requerido',
            'id_administrador.exists' => 'El Administrador no existe',
            'id_sucursal.required' => 'La Sucursal es requerida',
            'id_sucursal.exists' => 'La Sucursal no existe',
            'id_sucursal.not_in' => 'La Sucursal es requerida',
            'sueldo.required' => 'El Sueldo es requerido',
            'sueldo.numeric' => 'El sueldo tiene que ser de tipo numérico',
            'sueldo.between' => 'El sueldo tiene que estar entre 0 y 9999999.99'
            //'sueldo.digits_between' => 'El Sueldo solo tiene que tener digitos del 0 - 9'
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

        $cocinero = new Cocinero;
        $cocinero->primer_nombre = $request->get("primer_nombre");
        $cocinero->segundo_nombre = $request->get("segundo_nombre");
        $cocinero->paterno = $request->get("paterno");
        $cocinero->materno = $request->get("materno");
        $cocinero->email = $request->get("email");
        $cocinero->dni = $request->get("dni");
        $cocinero->sexo = $request->get("sexo");
        $cocinero->direccion = $request->get("direccion");
        $cocinero->nombre_usuario = $request->get("nombre_usuario");
        $cocinero->password = bcrypt($request->get("password"));
        $cocinero->fecha_nac = $request->get("fecha_nac");
        $cocinero->tipo_usuario = $request->get("tipo_usuario");
        $cocinero->celular = $request->get("celular");
        $cocinero->telefono = $request->get("telefono");
        $cocinero->tipo_usuario = $request->get("tipo_usuario");
        //Campos correspondientes solo al Cajero
        $cocinero->id_sucursal = $request->get("id_sucursal");
        $cocinero->id_administrador = $request->get("id_administrador");
        $cocinero->sueldo = $request->get("sueldo");
        $cocinero->estado = true;
        $cocinero->save();
        return response()->json(['data' => $cocinero], 201);
    }
    public function show($id)
    {
        $cocinero = DB::table('cocineros')
            ->select('id_cocinero as id_usuario', 'paterno', 'materno', 'primer_nombre', 'dni', 'direccion', 'nombre_usuario', 'email', 'direccion', 'fecha_nac', 'sueldo', 'id_sucursal', 'id_administrador', 'tipo_usuario', 'segundo_nombre', 'celular', 'telefono', 'sexo', 'estado')
            ->where('id_cocinero', '=', $id)
            ->get();
        return response()->json(['data' => $cocinero], 201);
    }
    public function update(Request $request, $id){
        \Log::info($request);
        $cocinero = Cocinero::find($id);
        $localRules = [
            'id_administrador' => 'required|exists:administradors,id_administrador',
            'id_sucursal' => 'required|not_in:-1|exists:sucursals,id_sucursal',
            'sueldo' => 'required|numeric|between:0,9999999.99',
        ];
        $locaMessages = [
            'id_administrador.required' => 'El Administrador es requerido',
            'id_administrador.exists' => 'El Administrador no existe',
            'id_sucursal.required' => 'La Sucursal es requerida',
            'id_sucursal.exists' => 'La Sucursal no existe',
            'id_sucursal.not_in' => 'La Sucursal es requerida',
            'sueldo.required' => 'El Sueldo es requerido',
            'sueldo.numeric' => 'El sueldo tiene que ser de tipo numérico',
            'sueldo.between' => 'El sueldo tiene que estar entre 0 y 9999999.99'
        ];
        $allRules = (new UserRequest())->rulesUpdate($cocinero->id_usuario) + $localRules;
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

        if($request->has('primer_nombre')) {
            $cocinero->primer_nombre = $request->get("primer_nombre");
        }
        if($request->has('segundo_nombre')) {
            $cocinero->segundo_nombre = $request->get("segundo_nombre");
        }
        if($request->has('paterno')) {
            $cocinero->paterno = $request->get("paterno");
        }
        if($request->has('materno')) {
            $cocinero->materno = $request->get("materno");
        }
        if($request->has('email')) {
            $cocinero->email = $request->get("email");
        }
        if($request->has('dni')) {
            $cocinero->dni = $request->get("dni");
        }
        if($request->has('sexo')) {
            $cocinero->sexo = $request->get("sexo");
        }
        if($request->has('estado')) {
            $cocinero->estado = $request->get("estado");
        }
        if($request->has('direccion')) {
            $cocinero->direccion = $request->get("direccion");
        }
        if($request->has('nombre_usuario')) {
            $cocinero->nombre_usuario = $request->get("nombre_usuario");
        }
        if($request->has('fecha_nac')) {
            $cocinero->fecha_nac = $request->get("fecha_nac");
        }
        if($request->has('tipo_usuario')) {
            $cocinero->tipo_usuario = $request->get("tipo_usuario");
        }
        if($request->has('celular')) {
            $cocinero->celular = $request->get("celular");
        }
        if($request->has('telefono')) {
            $cocinero->telefono = $request->get("telefono");
        }
        //Campos correspondientes solo al Cajero
        if($request->has('id_sucursal')) {
            $cocinero->id_sucursal = $request->get("id_sucursal");
        }
        if($request->has('id_administrador')) {
            $cocinero->id_administrador = $request->get("id_administrador");
        }
        if($request->has('sueldo')) {
            $cocinero->sueldo = $request->get("sueldo");
        }
        if(!$cocinero->isDirty()){
            return $this->errorResponse(['valores' => 'Se debe modificar al menos un valor para poder actualizar'], 201);
        }
        $cocinero->save();
        return response()->json(['data' => $cocinero], 201);
    }
    public function destroy($id){
        $cocinero = Cocinero::find($id)->delete();
        return response()->json(['data' => $cocinero], 201);
    }
}
