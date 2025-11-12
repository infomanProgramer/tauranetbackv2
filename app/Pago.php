<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $primaryKey = 'id_pago';
    protected $table = 'pagos';

    // protected $fillable = [
    //     'efectivo',
    //     'total',
    //     'total_pagar',
    //     'visa',
    //     'mastercard',
    //     'cambio',
    //     'id_venta_producto'
    // ];
}
