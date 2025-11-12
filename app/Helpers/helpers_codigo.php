<?php
function getSystemFingerprint() {
    $file = storage_path('app/system_id.txt');
    // Si ya existe el ID guardado, úsalo siempre
    if (file_exists($file)) {
        return trim(file_get_contents($file));
    }

    // Si no existe, genera uno nuevo basado en hardware y entorno
    $mac = @shell_exec('getmac');
    $disk = @shell_exec('wmic diskdrive get serialnumber 2>&1');
    $hostname = gethostname();

    // Si alguna llamada falla, se evita error usando valores vacíos
    $rawData = ($mac ?: '') . ($disk ?: '') . $hostname;

    // Genera un hash estable y lo guarda
    $fingerprint = md5($rawData);
    file_put_contents($file, $fingerprint);

    return $fingerprint;
}
?>