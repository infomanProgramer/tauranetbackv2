<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Validator;
use App\Restaurant;

class AdministradorAuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        \Log::info('Entrando a la ruta de login de AdministradorAuthController');
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

        if (! $token = auth('admin')->attempt($credentials)) {
            return response()->json(['error' => 'Datos incorrectos'], 200);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('admin')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('admin')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('admin')->refresh());
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
        $restaurante = Restaurant::find(auth('admin')->user()->id_restaurant);
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('admin')->factory()->getTTL() * 60,
            'type_user' => auth('admin')->user()->tipo_usuario,
            'id_restaurant' => auth('admin')->user()->id_restaurant,
            'sexo' => auth('admin')->user()->sexo,
            'tipo_moneda' => $restaurante->tipo_moneda
        ]);
    }
}
