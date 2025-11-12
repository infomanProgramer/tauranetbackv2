<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Superadministrador;
use App\Restaurant;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use Notifiable, SoftDeletes;

    protected $dates = ['deleted_at'];
    const USUARIO_ACTIVO = 'true';
    const USUARIO_NO_ACTIVO = 'false';

    const USUARIO_MASCULINO = 'true';
    const USUARIO_FEMENINO = 'false';

    protected $primaryKey = 'id_usuario';
    protected $fillable = [
        'primer_nommbre',
        'segundo_nombre',
        'paterno',
        'materno',
        'dni',
        'direccion',
        'nombre_usuario',
        'email',
        'password',
        'fecha_nac',
        'sexo',
        'nombre_fotoperfil',
        'id_superadministrador',
        'tipo_usuario',
        'id_restaurant',
        'telefono',
        'celular',
        'estado'
    ];
    protected $hidden = [
        'password'
    ];

    public function setCorreoAttribute($val){
        $this->attributes['email'] = strtolower($val);
    }

    public function usuarioEsActivo(){
        return $this->estado == User::USUARIO_ACTIVO;
    }
    public function usuarioEsMasculino(){
        return $this->sexo == User::USUARIO_MASCULINO;
    }

    /*Pertenece a */
    public function superadministradors(){
        return $this->belongsTo(Superadministrador::class);
    }
    public function restaurants(){
        return $this->belongsTo(Restaurant::class);
    }
    // Rest omitted for brevity
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
