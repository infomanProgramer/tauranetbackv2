<?php

namespace App;

use App\Cliente;
use App\VentaProducto;
use App\Sucursal;
use App\User;
use App\Administrador;
use Spatie\Permission\Traits\HasRoles;


class Cajero extends User
{
    use HasRoles;
    protected $guard_name = 'cajero';

    protected $primaryKey = 'id_cajero';
    protected $fillable = [
        'sueldo',
        'fecha_inicio',
        'id_sucursal',
        'id_usuario',
        'id_administrador',
        'id_cajero'
    ];

    public function clientes(){
        return $this->hasMany(Cliente::class);
    }
    public function venta_productos(){
        return $this->hasMany(VentaProducto::class);
    }
    /*Pertenece a */
    public function sucursals(){
        return $this->belongsTo(Sucursal::class);
    }
    public function usuarios(){
        return $this->belongsTo(User::class);
    }
    public function administradors(){
        return $this->belongsTo(Administrador::class);
    }
}
