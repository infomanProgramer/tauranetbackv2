<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\CategoriaProducto;
use DB;

class CategoriaProductoController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($pag, $idRestaurante)
    {
        $cproducto = DB::table('categoria_productos')
                    ->where('id_restaurant', '=', $idRestaurante)
                    ->whereNull('deleted_at')
                    ->orderBy('created_at', 'desc')
                    ->paginate($pag);
        return response()->json(['data' => $cproducto], 201);
    }
    public function combo($idRestaurante)
    {
        $cproducto = DB::table('categoria_productos')
            ->where('id_restaurant', '=', $idRestaurante)
            ->whereNull('deleted_at')
            ->orderBy('nombre', 'asc')
            ->get();
        return response()->json(['data' => $cproducto], 201);
    }
    public function combopersonal($idSucursal)
    {
        $cproducto = DB::table('sucursals as s')
            ->join('restaurants as r', 'r.id_restaurant', '=', 's.id_restaurant')
            ->join('categoria_productos as c', 'c.id_restaurant', '=', 'r.id_restaurant')
            ->where('s.id_sucursal', '=', $idSucursal)
            ->whereNull('c.deleted_at')
            ->select('c.*')
            ->orderBy('c.nombre', 'asc')
            ->get();
        return response()->json(['data' => $cproducto], 201);
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
     * Crea nueva categoria de productos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) 
    {
        \Log::info($request);
        $dataValidation = [
            'nombre' => 'required|min:4|max:15',
            'id_restaurant' => 'required|exists:restaurants,id_restaurant',
        ];
        if ($request->get("id_administrador") !== null) {
            $dataValidation = array_merge($dataValidation, [
                'id_administrador' => 'required|exists:administradors,id_administrador',
            ]);
        } elseif ($request->get("id_cajero") !== null) {
            $dataValidation = array_merge($dataValidation, [
                'id_cajero' => 'required|exists:cajeros,id_cajero',
            ]);
        }
        $validator = Validator::make($request->all(), $dataValidation,
        $messages = [
            'nombre.required' => 'La categoria es requerida',
            'nombre.min' => 'La categoria tiene que tener 4 caracteres como mínimo',
            'nombre.max' => 'La categoria tiene que tener 15 caracteres como maximo',
            'id_administrador.required' => 'El administrador es requerido',
            'id_administrador.exists' => 'El administrador no existe',
            'id_restaurant.required' => 'El restaurante es requerido',
            'id_restaurant.exists' => 'El restaurante no existe',
        ]);
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 201);
        }
        $cproducto = new CategoriaProducto();
        $cproducto->nombre = $request->get("nombre");
        $cproducto->descripcion = $request->get("descripcion");
        if ($request->get("id_administrador") !== null) {
            $cproducto->id_administrador = $request->get("id_administrador");
        } elseif ($request->get("id_cajero") !== null) {
            $cproducto->id_cajero = $request->get("id_cajero");
        }
        $cproducto->id_cajero = $request->get("id_cajero");
        $cproducto->id_restaurant = $request->get("id_restaurant");
        $cproducto->save();
        return response()->json(['data' => $cproducto], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cproducto = CategoriaProducto::find($id);
        return response()->json(['data' => $cproducto], 201);
    }

    public function update(Request $request, $id)
    {
        \Log::info($request);
        $cproducto = CategoriaProducto::find($id);
        $dataValidation = [
            'nombre' => 'required|min:4|max:15',
            'id_restaurant' => 'required|exists:restaurants,id_restaurant',
        ];
        if ($request->get("id_administrador") !== null) {
            $dataValidation = array_merge($dataValidation, [
                'id_administrador' => 'required|exists:administradors,id_administrador',
            ]);
        } elseif ($request->get("id_cajero") !== null) {
            $dataValidation = array_merge($dataValidation, [
                'id_cajero' => 'required|exists:cajeros,id_cajero',
            ]);
        }
        $validator = Validator::make($request->all(), $dataValidation,
        $messages = [
            'nombre.required' => 'La categoria es requerida',
            'nombre.min' => 'La categoria tiene que tener 4 caracteres como mínimo',
            'nombre.max' => 'La categoria tiene que tener 15 caracteres como maximo',
            'id_administrador.required' => 'El administrador es requerido',
            'id_administrador.exists' => 'El administrador no existe',
            'id_restaurant.required' => 'El restaurante es requerido',
            'id_restaurant.exists' => 'El restaurante no existe',
        ]);
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 201);
        }
        if($request->has('nombre')) {
            $cproducto->nombre = $request->get("nombre");
        }
        if($request->has('descripcion')) {
            $cproducto->descripcion = $request->get("descripcion");
        }
        if ($request->get("id_administrador") !== null) {
            $cproducto->id_administrador = $request->get("id_administrador");
        } elseif ($request->get("id_cajero") !== null) {
            $cproducto->id_cajero = $request->get("id_cajero");
        }
        if($request->has('id_restaurant')) {
            $cproducto->id_restaurant = $request->get("id_restaurant");
        }
        if(!$cproducto->isDirty()){
            return $this->errorResponse(['valores' => 'Se debe modificar al menos un valor para poder actualizar'], 201);
        }
        $cproducto->save();
        return response()->json(['data' => $cproducto], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Verifica que los productos no pertenezcan a la categoria
        $var = DB::table('productos as p')
                    ->join('categoria_productos as c', 'c.id_categoria_producto', '=','p.id_categoria_producto')
                    ->where('p.id_categoria_producto', '=', $id)
                    ->whereNull('p.deleted_at')
                    ->get();
        \Log::info(sizeof($var));
        if(sizeof($var) == 0){
            $cproducto = CategoriaProducto::find($id)->delete();
            return response()->json(['data' => $cproducto], 201);
        }else{
            $obj = ['error' => 'No se puede eliminar, existen productos asignados a la categoria'];
            return response()->json(['data' => $obj], 201);
        }

    }
}
