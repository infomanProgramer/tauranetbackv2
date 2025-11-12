<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Cajero;
use App\Administrador;
use App\Sucursal;

class Caja extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $primaryKey = 'id_caja';
    protected $fillable = [
        'nombre',
        'estado',
        'id_administrador',
        'id_sucursal',
    ];

    public function cajeros()
    {
        return $this->hasMany(Cajero::class);
    }

    public function administradors()
    {
        return $this->belongsTo(Administrador::class);
    }

    public function sucursals()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
