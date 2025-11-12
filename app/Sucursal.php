<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Empleado;
use App\SucursalCarta;
use App\Restaurant;
use App\Superadministrador;

class Sucursal extends Model
{
    const SUCURSAL_ACTIVO = 'true';
    const SUCURSAL_NO_ACTIVO = 'false';

    protected $primaryKey = 'id_sucursal';
    protected $fillable = [
        'nombre',
        'direccion',
        'descripcion',
        'id_restaurant',
        'id_superadministrador',
        'ciudad',
        'pais',
        'telefono',
        'celular'
    ];

    public function sucursalEsActivo(){
        return $this->estado == Sucursal::SUCURSAL_ACTIVO;
    }

    /*Relaciones*/
    public function empleados(){
        return $this->hasMany(Empleado::class);
    }
    public function sucursal_cartas(){
        return $this->hasMany(SucursalCarta::class);
    }
    /*Pertenece a */
    public function restaurants(){
        return $this->belongsTo(Restaurant::class);
    }
    public function superadministradors(){
        return $this->belongsTo(Superadministrador::class);
    }
}
