<?php

namespace App;

use App\CategoriaProducto;
use App\Producto;
use App\Empleado;
use App\Superadministrador;
use App\User;
use Spatie\Permission\Traits\HasRoles;

class Administrador extends User
{
    use HasRoles;
    protected $guard_name = 'admin';

    protected $primaryKey = 'id_administrador';
    protected $fillable = [
        'id_restaurant',
        'id_superadministrador'
    ];

    public function categoria_productos(){
        return $this->hasMany(CategoriaProducto::class);
    }
    public function productos(){
        return $this->hasMany(Producto::class);
    }
    public function empleados(){
        return $this->hasMany(Empleado::class);
    }
    /*Pertenece a */
    public function superadministradors(){
        return $this->belongsTo(Superadministrador::class);
    }
}
