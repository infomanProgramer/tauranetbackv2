<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use DB;
use App\Producto;
use Validator;
use File;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductoController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProducts($idRestaurante, $idCategoria){
        if($idCategoria == -1){
            $producto = DB::table('productos as p')
                ->join('categoria_productos as c', 'c.id_categoria_producto', '=', 'p.id_categoria_producto')
                ->where('c.id_restaurant', '=', $idRestaurante)
                ->whereNull('p.deleted_at')
                ->orderBy('p.created_at', 'desc')
                ->select('p.*', 'c.nombre as categoriaProducto');
        }else if($idCategoria != -1){
            $producto = DB::table('productos as p')
                ->join('categoria_productos as c', 'c.id_categoria_producto', '=', 'p.id_categoria_producto')
                ->where('c.id_restaurant', '=', $idRestaurante)
                ->where('p.id_categoria_producto', '=', $idCategoria)
                ->whereNull('p.deleted_at')
                ->orderBy('p.created_at', 'desc')
                ->select('p.*', 'c.nombre as categoriaProducto');
        }
        return $producto;
    }
    public function index($pag, $idRestaurante, $idCategoria){
        $productos = $this->getProducts($idRestaurante, $idCategoria)->paginate($pag);
        $response = Response::json(['data' => $productos], 200);
        return $response;
    }

    public function exportProductsToExcel($idRestaurante, $idCategoria){
        $productos = $this->getProducts($idRestaurante, $idCategoria)->get();
        $exportData = [];
        foreach ($productos as $row) {
            $exportData[] = [
                'Categoría' => $row->categoriaProducto,
                'Producto' => $row->nombre,
                'Descripción' => $row->descripcion,
                'Precio' => $row->precio
            ];
        }
        $export = new class($exportData) implements FromCollection, WithHeadings {
            private $data;
            public function __construct($data) { $this->data = $data; }
            public function collection() { return collect($this->data); }
            public function headings(): array {
                return ['Categoría', 'Producto', 'Descripción', 'Precio'];
            }
        };

        return Excel::download($export, 'reporte_productos.xlsx');
    }

    public function listaProductos($idSucursal, $idCategoria){
        $producto = null;
        if($idCategoria == -1){//Todas las categorias
            $producto = DB::table('productos as p')
                ->join('categoria_productos as c', 'c.id_categoria_producto', '=', 'p.id_categoria_producto')
                ->join('restaurants as r', 'r.id_restaurant', '=', 'c.id_restaurant')
                ->join('sucursals as s', 's.id_restaurant', '=', 'r.id_restaurant')
                ->where('s.id_sucursal', '=', $idSucursal)
                ->whereNull('p.deleted_at')
                ->select('p.*')
                ->orderBy('p.nombre', 'asc')
                ->get();
        }else{
            $producto = DB::table('productos as p')
                ->join('categoria_productos as c', 'c.id_categoria_producto', '=', 'p.id_categoria_producto')
                ->join('restaurants as r', 'r.id_restaurant', '=', 'c.id_restaurant')
                ->join('sucursals as s', 's.id_restaurant', '=', 'r.id_restaurant')
                ->where('s.id_sucursal', '=', $idSucursal)
                ->where('c.id_categoria_producto', '=', $idCategoria)
                ->whereNull('p.deleted_at')
                ->select('p.*')
                ->orderBy('p.nombre', 'asc')
                ->get();
        }
        $response = Response::json(['data' => $producto], 200);
        return $response;
    }

    public function store(Request $request)
    {
        $dataValidation = [
            'nombre' => 'required|min:4|max:21',
            'precio' => 'required|numeric|between:0,9999999.99',
            'descripcion' => 'nullable|min:4|max:22',
            'id_categoria_producto' => 'required|not_in:-1|exists:categoria_productos,id_categoria_producto',
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
            'nombre.required' => 'El Nombre es requerido',
            'nombre.min' => 'El Nombre tiene que tener 4 caracteres como mínimo',
            'nombre.max' => 'El Nombre tiene que tener 21 caracteres como maximo',
            'precio.required' => 'El precio es requerido',
            'precio.numeric' => 'El precio tiene que ser de tipo numérico',
            'precio.between' => 'El precio tiene ser un número positivo',
            'descripcion.min' => 'La Descripcion tiene que tener 4 caracteres como mínimo',
            'descripcion.max' => 'La Descripcion tiene que tener 22 caracteres como maximo',
            'id_administrador.required' => 'El Administrador es requerido',
            'id_administrador.exists' => 'El Administrador no existe',
            'id_categoria_producto.required' => 'La Categoria es requerida',
            'id_categoria_producto.not_in' => 'La Categoria es requerida',
            'id_categoria_producto.exists' => 'La Categoria no existe',
            ]);
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 201);
        }
        $producto = new Producto();
        $producto->nombre = $request->get("nombre");
        $producto->descripcion = $request->get("descripcion");
        $producto->precio = $request->get("precio");
        //$producto->precio_base = $request->get("precio_base");
        $producto->id_administrador = $request->get("id_administrador");
        if ($request->get("id_administrador") !== null) {
            $producto->id_administrador = $request->get("id_administrador");
        } elseif ($request->get("id_cajero") !== null) {
            $producto->id_cajero = $request->get("id_cajero");
        }
        $producto->id_categoria_producto = $request->get("id_categoria_producto");
        if($request->hasFile('image')){
            $producto->producto_image = $request->image->store('', 'imagesProductos');
        }
        $producto->save();
        return response()->json(['data' => $producto], 201);
    }
    public function show($id)
    {
        $producto = Producto::find($id);
        return response()->json(['data' => $producto], 201);
    }
    public function update(Request $request, $id)
    {
        $producto = Producto::find($id);
        $dataValidation = [
            'nombre' => 'required|min:4|max:21',
            'precio' => 'required|numeric|between:0,9999999.99',
            'descripcion' => 'nullable|min:4|max:22',
            'id_categoria_producto' => 'required|not_in:-1|exists:categoria_productos,id_categoria_producto',
        ];
        if ($request->get("id_administrador") !== null && $request->get("id_administrador") !== 'null') {
            $dataValidation = array_merge($dataValidation, [
                'id_administrador' => 'required|exists:administradors,id_administrador',
            ]);
        } elseif ($request->get("id_cajero") !== null && $request->get("id_cajero") !== 'null') {
            $dataValidation = array_merge($dataValidation, [
                'id_cajero' => 'required|exists:cajeros,id_cajero',
            ]);
        }
        $validator = Validator::make($request->all(), $dataValidation,
        $messages = [
            'nombre.required' => 'El Nombre es requerido',
            'nombre.min' => 'El Nombre tiene que tener 4 caracteres como mínimo',
            'nombre.max' => 'El Nombre tiene que tener 21 caracteres como maximo',
            'precio.required' => 'El precio es requerido',
            'precio.numeric' => 'El precio tiene que ser de tipo numérico',
            'precio.between' => 'El precio tiene ser un número positivo',
            'id_administrador.required' => 'El Administrador es requerido',
            'id_administrador.exists' => 'El Administrador no existe',
            'id_categoria_producto.required' => 'La Categoria es requerida',
            'id_categoria_producto.not_in' => 'La Categoria es requerida',
            'id_categoria_producto.exists' => 'La Categoria no existe',
            'descripcion.min' => 'La Descripcion tiene que tener 4 caracteres como mínimo',
            'descripcion.max' => 'La Descripcion tiene que tener 22 caracteres como maximo',
        ]);
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 201);
        }
        if($request->has('nombre') && $request->nombre != $producto->nombre) {
            $producto->nombre = $request->get("nombre");
        }
        if($request->has('descripcion') && $request->descripcion != 'null') {
            $producto->descripcion = $request->get("descripcion");
        }
        if($request->has('precio')) {
            $producto->precio = $request->get("precio");
        }
        if ($request->get("id_administrador") !== null && $request->get("id_administrador") !== 'null') {
            $producto->id_administrador = $request->get("id_administrador");
        } elseif ($request->get("id_cajero") !== null) {
            $producto->id_cajero = $request->get("id_cajero");
        }
        if($request->has('id_categoria_producto')) {
            $producto->id_categoria_producto = $request->get("id_categoria_producto");
        }
        if($request->hasFile('image')){
            $producto->producto_image = $request->image->store('', 'imagesProductos');
        }
        if(!$producto->isDirty()){
            return $this->errorResponse(['valores' => 'Se debe modificar al menos un valor para poder actualizar'], 201);
        }
        $producto->save();
        return response()->json(['data' => $producto], 201);
    }
    public function destroy($id)
    {
        $producto = Producto::find($id)->delete();
        return response()->json(['data' => $producto], 201);
    }
    public function destroyImage($id){
        $producto = Producto::findOrFail($id);
        File::delete('imgProductos/'.$producto->producto_image);
        $producto->producto_image = null;
        $producto->save();
        $response = Response::json(['data' => $producto], 200);
        return $response;
    }
}
