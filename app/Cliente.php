<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\VentaProducto;
use App\Empleado;
use App\Sucursal;

class Cliente extends Model
{
    protected $primaryKey = 'id_cliente';
    protected $fillable = [
        'nombre_completo',
        'dni',
        'id_cajero',
        'id_mozo',
        'id_sucursal',
        'id_restaurant'
    ];
}
