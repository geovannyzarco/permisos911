<?php
// Configuración de la base de datos
$serverName = "192.168.120.1, 1433";
$connectionOptions = array(
    "Database" => "permisos_dev",
    "Uid" => "gescobar",
    "PWD" => "100504",
    "CharacterSet" => "UTF-8"
);

// Establecer la conexión
$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>
