<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Cajero;
use Illuminate\Support\Facades\Hash;
use Validator;
use DB;
use Log;

class CajeroController extends ApiController
{
    public function store(Request $request){
        $localRules = [
            'id_administrador' => 'nullable|exists:administradors,id_administrador',
            'id_superadministrador' => 'nullable|exists:superadministradors,id_superadministrador',
            'id_restaurant' => 'required|not_in:-1|exists:restaurants,id_restaurant',
            'id_sucursal' => 'required|not_in:-1|exists:sucursals,id_sucursal',
            'id_caja' => 'required|not_in:-1|exists:cajas,id_caja',
            function ($attribute, $value, $fail) {
                if (!$this->has('id_administrador') && !$this->has('id_superadministrador')) {
                    $fail('Debe ingresar al menos un campo entre id_administrador o id_superadministrador');
                }
            }
        ];
        $locaMessages = [
            'id_administrador.required' => 'El Administrador es requerido',
            'id_administrador.exists' => 'El Administrador no existe',
            'id_superadministrador.required' => 'El Super Administrador es requerido',
            'id_superadministrador.exists' => 'El Super Administrador no existe',
            'id_sucursal.required' => 'La Sucursal es requerida',
            'id_sucursal.exists' => 'La Sucursal no existe',
            'id_sucursal.not_in' => 'La Sucursal es requerida',
            'id_caja.required' => 'La Caja es requerida',
            'id_caja.not_in' => 'La Caja es requerida',
            'id_caja.exists' => 'La Caja no existe',
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

        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 201);
        }

        $cajero = new Cajero;
        $cajero->primer_nombre = $request->get("primer_nombre");
        $cajero->segundo_nombre = $request->get("segundo_nombre");
        $cajero->paterno = $request->get("paterno");
        $cajero->materno = $request->get("materno");
        $cajero->nombre_usuario = $request->get("nombre_usuario");
        $cajero->password = bcrypt($request->get("password"));
        $cajero->tipo_usuario = $request->get("tipo_usuario");
        //Campos correspondientes solo al Cajero
        
        if ($request->has("id_administrador")) {
            $cajero->id_administrador = $request->get("id_administrador");
        }
        if ($request->has("id_superadministrador")) {
            $cajero->id_superadministrador = $request->get("id_superadministrador");
        }

        $cajero->id_caja = $request->get("id_caja");
        $cajero->estado = true;
        $cajero->save();
        return response()->json(['data' => $cajero], 201);
    }

    public function update(Request $request, $id){
        \Log::info($request);
        $cajero = Cajero::find($id);
        $localRules = [
            'id_superadministrador' => 'required|exists:superadministradors,id_superadministrador',
            'id_sucursal' => 'required|not_in:-1|exists:sucursals,id_sucursal',
            'id_restaurant' => 'required|not_in:-1|exists:restaurants,id_restaurant',
            'id_caja' => 'required|not_in:-1|exists:cajas,id_caja'
        ];
        $locaMessages = [
            'id_superadministrador.required' => 'El Super Administrador es requerido',
            'id_superadministrador.exists' => 'El Super Administrador no existe',
            'id_sucursal.required' => 'La Sucursal es requerida',
            'id_sucursal.exists' => 'La Sucursal no existe',
            'id_sucursal.not_in' => 'La Sucursal es requerida',
            'id_caja.required' => 'La Caja es requerida',
            'id_caja.not_in' => 'La Caja es requerida',
            'id_caja.exists' => 'La Caja no existe'
        ];
        $allRules = (new UserRequest())->rulesUpdate($cajero->id_usuario) + $localRules;
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

        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 201);
        }

        if($request->has('primer_nombre')) {
            $cajero->primer_nombre = $request->get("primer_nombre");
        }
        if($request->has('segundo_nombre')) {
            $cajero->segundo_nombre = $request->get("segundo_nombre");
        }
        if($request->has('paterno')) {
            $cajero->paterno = $request->get("paterno");
        }
        if($request->has('materno')) {
            $cajero->materno = $request->get("materno");
        }
        if($request->has('estado')) {
            $cajero->estado = $request->get("estado");
        }
        if($request->has('nombre_usuario')) {
            $cajero->nombre_usuario = $request->get("nombre_usuario");
        }
        if($request->has('tipo_usuario')) {
            $cajero->tipo_usuario = $request->get("tipo_usuario");
        }
        //Campos correspondientes solo al Cajero
        if($request->has('id_superadministrador')) {
            $cajero->id_superadministrador = $request->get("id_superadministrador");
        }
        if($request->has('id_caja')) {
            $cajero->id_caja = $request->get("id_caja");
        }
        if(!$cajero->isDirty()){
            return $this->errorResponse(['valores' => 'Se debe modificar al menos un valor para poder actualizar'], 201);
        }
        $cajero->save();
        return response()->json(['data' => $cajero], 201);
    }
    public function show($id){
        Log::info("id cajero: ". $id);
        $mac = exec('getmac');
        //$mac = strtok($mac, ' '); // primera MAC address
        Log::info("mac: ". $mac);
        $cajero = DB::table('cajeros as c')
                  ->join('cajas as ca', 'ca.id_caja', '=', 'c.id_caja')
                  ->join('sucursals as s', 's.id_sucursal', '=', 'ca.id_sucursal')
                  ->where('id_cajero', '=', $id)
                  ->select(
                    'c.id_cajero as id_usuario', 
                    'c.paterno', 
                    'c.materno', 
                    'c.primer_nombre', 
                    'c.dni', 
                    'c.direccion', 
                    'c.nombre_usuario', 
                    'c.email', 
                    'c.direccion', 
                    'c.fecha_nac', 
                    'c.sueldo', 
                    'c.id_superadministrador', 
                    'c.id_administrador', 
                    'c.tipo_usuario', 
                    'c.id_caja', 
                    'ca.id_sucursal',
                    's.id_restaurant',
                    'c.segundo_nombre', 
                    'c.celular', 
                    'c.telefono', 
                    'c.sexo', 
                    'c.estado'
                  )
                  ->get();
        return response()->json(['data' => $cajero], 201);
    }
    public function destroy($id){
        $cajero = Cajero::find($id)->delete();
        return response()->json(['data' => $cajero], 201);
    }
    public function getGestionesPagos(){
        $anios = DB::table('pagos')
            ->selectRaw('DISTINCT EXTRACT(YEAR FROM created_at) AS anio')
            ->pluck('anio');
        return response()->json(['data' => $anios], 201);
    }
}
