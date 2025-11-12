<?php

namespace App\Http\Controllers;

use App\Cajero;
use App\Mozo;
use App\Administrador;
use App\User;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response;
use DB;

use App\Perfilimagen;

class PerfilimagenController extends Controller
{
    public function uploadImagePerfil(Request $request){
        $imageName = $request->image->store('', 'imagesUsuario');
        $nameImageFile = explode('.', $imageName);
        if($nameImageFile[sizeof($nameImageFile)-1] == 'jpeg' || $nameImageFile[sizeof($nameImageFile)-1] == 'jpg' || $nameImageFile[sizeof($nameImageFile)-1] == 'png'){
            $imagen = new Perfilimagen;
            $imagen->nombre = $imageName;
            $imagen->save();
            return response()->json(['data' => $imagen], 201);
        }else{
            return response()->json(["error" => ["fotoperfil_url" => ["La imagen no tiene el formato correcto"]]], 201);
        }
    }
    public function destroy($id)
{
    $imagen = Perfilimagen::findOrFail($id);
    $imagen->delete();
    File::delete('imgPerfilUsuarios/'.$imagen->nombre);
    $response = Response::json(['data' => $imagen], 200);
    return $response;
}
    public function updateImagePerfil(Request $request, $idImg, $id_usr, $tipo)
    {
        //tipo = 0 (Administrador)
        //tipo = 1 (Cajero)
        //tipo = 2 (Mozo)
        $imagen = Perfilimagen::find($idImg);
        if($tipo == 0){
            $administrador = Administrador::find($id_usr);
            $imagen->id_administrador = $administrador->id_administrador;
            $imagen->save();
            $administrador->save();
        }else if($tipo == 1){
            $cajero = Cajero::find($id_usr);
            $imagen->id_cajero = $cajero->id_cajero;
            $imagen->save();
            $cajero->save();
        }else if($tipo == 2){
            $mozo = Mozo::find($id_usr);
            $imagen->id_mozo = $mozo->id_mozo;
            $imagen->save();
            $mozo->save();
        }
        return response()->json(['data' => $imagen], 201);
    }
    public function show($id, $tipo)
    {
        //tipo = 0 (Administrador)
        //tipo = 1 (Cajero)
        //tipo = 2 (Mozo)
        $id_imagen = null;
        if($tipo == 0){
            $id_imagen = DB::table('perfilimagens as p')
                ->where('p.id_administrador', '=', $id)
                ->select('id_perfilimagen')
                ->get();
        }else if($tipo == 1){
            $id_imagen = DB::table('perfilimagens as p')
                ->where('p.id_cajero', '=', $id)
                ->select('id_perfilimagen')
                ->get();
        }else if($tipo == 2){
            $id_imagen = DB::table('perfilimagens as p')
                ->where('p.id_mozo', '=', $id)
                ->select('id_perfilimagen')
                ->get();
        }
        if(sizeof($id_imagen)>0){
            $imagen = Perfilimagen::findOrFail($id_imagen[0]->id_perfilimagen);
            return response()->json(['data' => $imagen], 201);
        }else{
            return response()->json(['data' => 0], 201);
        }
    }
}