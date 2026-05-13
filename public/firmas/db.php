<?php

// Función para leer el archivo .env manualmente
function getCustomEnv($key, $default = null) {
    $path = __DIR__ . '/../../.env';
    if (file_exists($path)) {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            list($name, $value) = explode('=', $line, 2);
            if (trim($name) == $key) {
                return trim($value, " \t\n\r\0\x0B\"");
            }
        }
    }
    return $default;
}

// Configuración de la base de datos usando el .env
$serverName = getCustomEnv('DB_HOST', '192.168.120.1') . ', ' . getCustomEnv('DB_PORT', '1433');
$connectionOptions = array(
    "Database" => getCustomEnv('DB_DATABASE', 'permisos'),
    "Uid" => getCustomEnv('DB_USERNAME', 'gescobar'),
    "PWD" => getCustomEnv('DB_PASSWORD', '100504'),
    "CharacterSet" => "UTF-8",
    "TrustServerCertificate" => true
);

// Establecer la conexión
$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    header('Content-Type: application/json');
    die(json_encode(['success' => false, 'message' => 'Error de conexión: ' . print_r(sqlsrv_errors(), true)]));
}
