<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Restaurant;
use App\Superadministrador;

class Suscripcion extends Model
{
    protected $primaryKey = 'id_suscripcion';
    protected $fillable = [
        'tipo_suscripcion',
        'observacion',
        'precio_anual',
        'precio_mensual',
        'id_superadministrador'
    ];
    public function restaurants(){
        return $this->hasMany(Restaurant::class);
    }
    public function superadministradors(){
        return $this->belongsTo(Superadministrador::class);
    }
}
