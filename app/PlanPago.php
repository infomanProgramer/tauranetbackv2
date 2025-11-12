<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanPago extends Model
{
    protected $primaryKey = 'id_planpago';
    protected $table = 'plan_de_pagos';

    protected $fillable = [
        'cant_pedidos',
        'cant_mozos',
        'cant_cajas',
        'id_suscripcion'
    ];
}
