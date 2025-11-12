<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Perfilimagen extends Model
{
    protected $primaryKey = 'id_perfilimagen';

    protected $fillable = [
        'nombre',
        'id_administrador',
        'id_mozo',
        'id_cajero'
    ];
}
