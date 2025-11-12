<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Mozo;
use Validator;
use DB;

class MozoController extends ApiController
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

        $mozo = new Mozo;
        $mozo->primer_nombre = $request->get("primer_nombre");
        $mozo->segundo_nombre = $request->get("segundo_nombre");
        $mozo->paterno = $request->get("paterno");
        $mozo->materno = $request->get("materno");
        $mozo->email = $request->get("email");
        $mozo->dni = $request->get("dni");
        $mozo->sexo = $request->get("sexo");
        $mozo->direccion = $request->get("direccion");
        $mozo->nombre_usuario = $request->get("nombre_usuario");
        $mozo->password = bcrypt($request->get("password"));
        $mozo->fecha_nac = $request->get("fecha_nac");
        $mozo->tipo_usuario = $request->get("tipo_usuario");
        $mozo->celular = $request->get("celular");
        $mozo->telefono = $request->get("telefono");
        //Campos correspondientes solo al Cajero
        $mozo->id_sucursal = $request->get("id_sucursal");
        $mozo->id_administrador = $request->get("id_administrador");
        $mozo->sueldo = $request->get("sueldo");
        $mozo->estado = true;
        $mozo->save();
        return response()->json(['data' => $mozo], 201);
    }
    public function update(Request $request, $id){
        $mozo = Mozo::find($id);
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
        $allRules = (new UserRequest())->rulesUpdate($mozo->id_usuario) + $localRules;
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
            $mozo->primer_nombre = $request->get("primer_nombre");
        }
        if($request->has('segundo_nombre')) {
            $mozo->segundo_nombre = $request->get("segundo_nombre");
        }
        if($request->has('paterno')) {
            $mozo->paterno = $request->get("paterno");
        }
        if($request->has('materno')) {
            $mozo->materno = $request->get("materno");
        }
        if($request->has('email')) {
            $mozo->email = $request->get("email");
        }
        if($request->has('dni')) {
            $mozo->dni = $request->get("dni");
        }
        if($request->has('sexo')) {
            $mozo->sexo = $request->get("sexo");
        }
        if($request->has('estado')) {
            $mozo->estado = $request->get("estado");
        }
        if($request->has('direccion')) {
            $mozo->direccion = $request->get("direccion");
        }
        if($request->has('nombre_usuario')) {
            $mozo->nombre_usuario = $request->get("nombre_usuario");
        }
        if($request->has('fecha_nac')) {
            $mozo->fecha_nac = $request->get("fecha_nac");
        }
        if($request->has('tipo_usuario')) {
            $mozo->tipo_usuario = $request->get("tipo_usuario");
        }
        if($request->has('celular')) {
            $mozo->celular = $request->get("celular");
        }
        if($request->has('telefono')) {
            $mozo->telefono = $request->get("telefono");
        }
        //Campos correspondientes solo al Cajero
        if($request->has('id_sucursal')) {
            $mozo->id_sucursal = $request->get("id_sucursal");
        }
        if($request->has('id_administrador')) {
            $mozo->id_administrador = $request->get("id_administrador");
        }
        if($request->has('sueldo')) {
            $mozo->sueldo = $request->get("sueldo");
        }
        if(!$mozo->isDirty()){
            return $this->errorResponse(['valores' => 'Se debe modificar al menos un valor para poder actualizar'], 201);
        }
        $mozo->save();
        return response()->json(['data' => $mozo], 201);
    }
    public function show($id)
    {
        $mozo = DB::table('mozos')
            ->select('id_mozo as id_usuario', 'paterno', 'materno', 'primer_nombre', 'dni', 'direccion', 'nombre_usuario', 'email', 'direccion', 'fecha_nac', 'sueldo', 'id_sucursal', 'id_administrador', 'tipo_usuario', 'segundo_nombre', 'celular', 'telefono', 'sexo', 'estado')
            ->where('id_mozo', '=', $id)
            ->get();
        return response()->json(['data' => $mozo], 201);
    }
    public function destroy($id){
        $mozo = Mozo::find($id)->delete();
        return response()->json(['data' => $mozo], 201);
    }
}
