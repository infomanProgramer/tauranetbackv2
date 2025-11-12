<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistorialCaja extends Model
{

    protected $primaryKey = 'id_historial_caja';
    protected $table = 'historial_caja';

    protected $fillable = [
        'monto_inicial',
        'monto',
        'estado',
        'fecha',
        'id_administrador',
        'id_cajero',
        'id_caja'
    ];
}
