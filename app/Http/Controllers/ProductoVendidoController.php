<?php

namespace App\Http\Controllers;

use App\ProductoVendido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ComandaController;
use Validator;
use DB;

class ProductoVendidoController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($idVentaProducto)
    {
        $comandaController = new ComandaController();
        $datosComanda = $comandaController->queryComanda($idVentaProducto);
        $response = Response::json(['datosComanda' => $datosComanda], 200);
        return $response;
    }
}
