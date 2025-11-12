<?php

namespace App\Helpers;

class LicenseHelper
{
    public static function verifyLicense(): array
    {
        $licensePath = storage_path('app/license/license.dat');
        $publicKeyPath = storage_path('app/license/public.pem');

        if (!file_exists($licensePath) || !file_exists($publicKeyPath)) {
            return ['valid' => false, 'message' => 'No se encontró la licencia o la clave pública.'];
        }

        $licenseData = base64_decode(file_get_contents($licensePath));
        list($data, $signature) = explode('::', $licenseData);
        $dataArray = json_decode($data, true);

        if (!$dataArray) {
            return ['valid' => false, 'message' => 'Licencia corrupta o ilegible.'];
        }

        $publicKey = file_get_contents($publicKeyPath);
        if (!openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA256)) {
            return ['valid' => false, 'message' => 'Licencia inválida o manipulada.'];
        }

        // Importa la función del helper original
        require_once __DIR__ . '/helpers.php';
        if ($dataArray['fingerprint'] !== getSystemFingerprint()) {
            return ['valid' => false, 'message' => 'La licencia no corresponde a este equipo.'];
        }

        if (strtotime($dataArray['expires_at']) < time()) {
            return ['valid' => false, 'message' => 'Licencia expirada.'];
        }

        return [
            'valid' => true,
            'client' => $dataArray['client'],
            'type' => $dataArray['type'],
            'expires_at' => $dataArray['expires_at'],
        ];
    }
}

