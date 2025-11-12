<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Cliente;
use App\Empleado;
use App\Sucursal;

class VentaProducto extends Model
{
    protected $primaryKey = 'id_venta_producto';
    // protected $fillable = [
    //     'nro_venta',
    //     'total',
    //     'sub_total',
    //     'descuento',
    //     'estado_venta',
    //     'fecha',
    //     'id_cajero',
    //     'id_sucursal',
    //     'id_cliente',
    //     'id_mozo',
    //     'id_historial_caja',
    //     'estado_atendido',
    //     'id_cocinero'
    // ];

    /*Pertenece a */
    public function clientes(){
        return $this->belongsTo(Cliente::class);
    }
    public function empleados(){
        return $this->belongsTo(Empleado::class);
    }
    public function sucusals(){
        return $this->belongsTo(Sucursal::class);
    }
}
