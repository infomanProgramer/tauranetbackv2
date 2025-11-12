<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Sucursal;
use App\CategoriaProducto;
use App\Superadministrador;
use App\Suscripcion;

class Restaurant extends Model
{
    const RESTAURANT_ACTIVO = 'true';
    const RESTAURANT_NO_ACTIVO = 'false';

    protected $primaryKey = 'id_restaurant';
    protected $fillable = [
        'nombre',
        'estado',
        'descripcion',
        'observacion',
        'tipo_moneda',
        'identificacion',
        'id_superadministrador',
        'id_suscripcion'
    ];

    public function restaurantEsActivo(){
        return $this->estado == Restaurant::RESTAURANT_ACTIVO;
    }

    /*Relaciones*/
    public function sucursals(){
        return $this->hasMany(Sucursal::class);
    }
    public function categoria_productos(){
        return $this->hasMany(CategoriaProducto::class);
    }
    /*Pertenece a */
    public function superadministradors(){
        return $this->belongsTo(Superadministrador::class);
    }
    public function suscripcions(){
        return $this->belongsTo(Suscripcion::class);
    }
}
