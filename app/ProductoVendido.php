<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Producto;
use App\VentaProducto;

class ProductoVendido extends Model
{
    protected $primaryKey = 'id_producto_vendido';
    protected $fillable = [
        'cantidad',
        'importe',
        'nota',
        'p_unit',
        'id_producto',
        'id_venta_producto'
    ];
    public $timestamps = false;
    
    /*Pertenece a */
    public function productos(){
        return $this->belongsTo(Producto::class);
    }
    public function venta_productos(){
        return $this->belongsTo(VentaProducto::class);
    }
}
