<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Validator;
use DB;

class CajeroAuthController extends Controller
{

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        \Log::info('Usuario cajero autenticado prueba de logs:');
        $validator = Validator::make($request->all(), [
            'nombre_usuario' => 'required',
            'password'=> 'required'
        ],$messages = [
            'nombre_usuario.required' => 'El nombre de usuario es requerido',
            'password.required' => 'El password es requerido'
        ]);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()]);
        }
        $credentials = request(['nombre_usuario', 'password']);

        if (! $token = auth('cajero')->attempt($credentials)) {
            return response()->json(['error' => 'Datos incorrectos'], 200);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(){
        // \Log::info('Usuario autenticado prueba de logs:');
        // return response()->json(auth('cajero')->user());

        $user = \DB::table('cajeros as c')
        ->join('cajas as ca', 'ca.id_caja', '=', 'c.id_caja')
        ->join('sucursals as s', 's.id_sucursal', '=', 'ca.id_sucursal')
        ->where('c.id_cajero', auth('cajero')->user()->id_cajero)
        ->select(
            'c.id_usuario',
            'c.primer_nombre',
            'c.paterno',
            'c.materno',
            'c.dni',
            'c.direccion',
            'c.nombre_usuario',
            'c.email',
            'c.fecha_nac',
            'c.sexo',
            'c.nombre_fotoperfil',
            'c.tipo_usuario',
            'c.api_token',
            'c.segundo_nombre',
            'c.celular',
            'c.telefono',
            'c.estado',
            'c.id_cajero',
            'c.sueldo',
            'c.fecha_inicio',
            'c.id_administrador',
            'c.id_caja',
            'c.id_superadministrador',
            's.id_sucursal')
        ->first();

        return response()->json($user);
    }

    /**
     * Log the user out (Invalidate the token).
     *<
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(){
        auth('cajero')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('cajero')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $idRestaurant = DB::table('cajas as c')
            ->join('sucursals as s', 's.id_sucursal', '=', 'c.id_sucursal')
            ->where('c.id_caja', '=', auth('cajero')->user()->id_caja)
            ->select('s.id_restaurant')
            ->first();
        \Log::info($idRestaurant->id_restaurant);
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('cajero')->factory()->getTTL() * 60,
            'type_user' => auth('cajero')->user()->tipo_usuario,
            'id_sucursal' => auth('cajero')->user()->id_sucursal,
            'id_restaurant' => $idRestaurant->id_restaurant,
            'sexo' => auth('cajero')->user()->sexo
        ]);
    }
}
