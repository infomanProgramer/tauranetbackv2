<?php

namespace App\Http\Controllers;

use App\Administrador;
use App\Mozo;
use App\Superadministrador;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Cajero;
use App\Cocinero;

class ChangePasswordController extends ApiController
{
    public function changePassword(Request $request, $id, $type_user){
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6|confirmed',
            'password_ant' => 'required',
        ],
        $messages = [
            'password_ant.required' => 'El password es requerido',
            'password.required' => 'El Passoword Nuevo es requerido',
            'password.min' => 'El Password tiene que tener 6 caracteres como mínimo',
            'password.confirmed' => 'El Password debé ser confirmado',
        ]);
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 201);
        }
        if($type_user == 0){//Mozo
            $mozo = Mozo::find($id);
            if(Hash::check($request->password_ant, $mozo->password)){
                if($request->has('password')) {
                    $mozo->password = Hash::make($request->password);
                }
            }else{
                return response()->json(['error' => ['password_ant' => ['Password incorrecto']]], 201);
            }
            $mozo->save();
            return response()->json(['data' => $mozo], 201);
        }else if($type_user == 1) {//Cajero
            $cajero = Cajero::find($id);
            if(Hash::check($request->password_ant, $cajero->password)){
                if($request->has('password')) {
                    $cajero->password = Hash::make($request->password);
                }
            }else{
                return response()->json(['error' => ['password_ant' => ['Password incorrecto']]], 201);
            }
            $cajero->save();
            return response()->json(['data' => $cajero], 201);
        }else if($type_user == 2) {//Administrador
            $administrador = Administrador::find($id);
            if(Hash::check($request->password_ant, $administrador->password)){
                if($request->has('password')) {
                    $administrador->password = Hash::make($request->password);
                }
            }else{
                return response()->json(['error' => ['password_ant' => ['Password incorrecto']]], 201);
            }
            $administrador->save();
            return response()->json(['data' => $administrador], 201);
        }else if($type_user == 3) {//Superadministrador
            \Log::info("Cambiar contraseña superadministrador = ".$id);
            $superadministrador = Superadministrador::find($id);
            if(Hash::check($request->password_ant, $superadministrador->password)){
                if($request->has('password')) {
                    $superadministrador->password = Hash::make($request->password);
                }
            }else{
                return response()->json(['error' => ['password_ant' => ['Password incorrecto']]], 201);
            }
            $superadministrador->save();
            return response()->json(['data' => $superadministrador], 201);
        }else if($type_user == 4) {//Cocinero
            $cocinero = Cocinero::find($id);
            if(Hash::check($request->password_ant, $cocinero->password)){
                if($request->has('password')) {
                    $cocinero->password = Hash::make($request->password);
                }
            }else{
                return response()->json(['error' => ['password_ant' => ['Password incorrecto']]], 201);
            }
            $cocinero->save();
            return response()->json(['data' => $cocinero], 201);
        }
    }
}
