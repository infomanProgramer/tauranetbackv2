<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\ProductoVendido;
use App\CategoriaProducto;
use App\Administrador;

class Producto extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    const PRODUCTO_ACTIVO = 'true';
    const PRODUCTO_NO_ACTIVO = 'false';

    protected $primaryKey = 'id_producto';
    protected $fillable = [
        'nombre',
        'precio',
        'descripcion',
        'id_categoria_producto',
        'id_administrador'
    ];

    public function productoEsActivo(){
        return $this->estado == Producto::PRODUCTO_ACTIVO;
    }

    /*Relaciones*/
    public function producto_vendidos(){
        return $this->hasMany(ProductoVendido::class);
    }
    /*Pertenece a */
    public function categoria_productos(){
        return $this->belongsTo(CategoriaProducto::class);
    }
    /*Pertenece a */
    public function administradors(){
        return $this->belongsTo(Administrador::class);
    }
}
