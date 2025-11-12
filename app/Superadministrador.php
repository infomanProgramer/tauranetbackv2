<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Restaurant;
use App\Administrador;
use App\Sucursal;

class Superadministrador extends Authenticatable implements JWTSubject
{
    protected $primaryKey = 'id_superadministrador';
    protected $hidden = ['password'];
    protected $fillable = ['nombre_usuario', 'password'];

    public function restaurants(){
        return $this->hasMany(Restaurant::class);
    }
    public function administradors(){
        return $this->hasMany(Administrador::class);
    }
    public function sucursals(){
        return $this->hasMany(Sucursal::class);
    }
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
