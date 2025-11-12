<?php

namespace App\Http\Controllers;

use App\Restaurant;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ConsultaController extends Controller
{
    public function index(){
        $restaurante = Restaurant::all();
        $response = Response::json(['data' => $restaurante]);
        return $response;
    }
}
