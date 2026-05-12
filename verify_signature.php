<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Empleado;

$empleado = Empleado::where('oni', 'ep00116')->first();

if ($empleado) {
    echo "Empleado: " . $empleado->nombre . "\n";
    echo "ONI: " . $empleado->oni . "\n";
    echo "Firma (primeros 100 caracteres): " . substr($empleado->firma, 0, 100) . "...\n";
    echo "Longitud total de la firma: " . strlen($empleado->firma) . " caracteres.\n";
    
    if (str_starts_with($empleado->firma, 'data:image/png;base64,')) {
        echo "\n✅ EL FORMATO ES CORRECTO (Base64 Data URL).\n";
    } else {
        echo "\n❌ EL FORMATO NO ES EL ESPERADO.\n";
    }
} else {
    echo "Empleado no encontrado.\n";
}
