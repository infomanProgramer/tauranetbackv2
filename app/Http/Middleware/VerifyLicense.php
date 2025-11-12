<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\LicenseHelper;

class VerifyLicense
{
    public function handle(Request $request, Closure $next)
    {
        // Permitir acceder a la página de error sin verificar licencia para evitar bucles
        if ($request->is('license-error')) {
            return $next($request);
        }

        $license = LicenseHelper::verifyLicense();

        if (!$license['valid']) {
            // Para rutas API devolvemos JSON 403; para cualquier otra ruta, redirigimos a la vista CLI
            if ($request->is('api/*')) {
                return response()->json(['message' => $license['message']], 403);
            }
            return redirect()->route('license.error', ['m' => $license['message']]);
        }

        return $next($request);
    }
}
