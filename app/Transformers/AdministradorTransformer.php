<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Administrador;

class AdministradorTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Administrador $administrador)
    {
        return [
            'idAdministrador' => (int) $administrador->id_administrador,
            'nombres' => (string) $administrador->nombres,
            'paterno' => (string) $administrador->paterno,
            'ci' => (string) $administrador->dni,
            'direccion' => (string) $administrador->direccion,
            'nombreUsuario' => (string) $administrador->nombre_usuario,
            'correo' => (string) $administrador->correo,
            'password' => (string) $administrador->pass,
            'estado' => (boolean) $administrador->estado,
            'fechaNacimiento' => $administrador->fecha_nac,
            'ci' => (string) $administrador->dni,
            'sexo' => (boolean) $administrador->sexo,
            'fotoPerfilUrl' => isset($administrador->fotoperfil_url)? url("img/{".$administrador->fotoperfil_url."}"): null,
            'FechaCreacion' => isset($administrador->created_at)? $administrador->created_at: null,
            'FechaActualizacion' => isset($administrador->updated_at)? $administrador->updated_at: null,
            'IdRestaurant' => (int) $administrador->id_restaurant
        ];
    }
}
