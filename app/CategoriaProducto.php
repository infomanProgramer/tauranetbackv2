<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Producto;
use App\Administrador;
use App\Restaurant;

class CategoriaProducto extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    const CATEGORIA_PRODUCTO_ACTIVO = 'true';
    const CATEGORIA_PRODUCTO_NO_ACTIVO = 'false';

    protected $primaryKey = 'id_categoria_producto';
    // protected $fillable = [
    //     'nombre',
    //     'descripcion',
    //     'id_restaurant',
    //     'id_administrador'
    // ];

    public function categoriaProductoEsActivo(){
        return $this->estado == CategoriaProducto::CATEGORIA_PRODUCTO_ACTIVO;
    }
    /*Relaciones*/
    // public function productos(){
    //     return $this->hasMany(Producto::class);
    // }
    // public function administradors(){
    //     return $this->belongsTo(Administrador::class);
    // }
    // public function restaurants(){
    //     return $this->belongsTo(Restaurant::class);
    // }
}
