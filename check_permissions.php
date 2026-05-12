<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Permission;

$permisos = Permission::where('name', 'like', '%permiso%')->pluck('name');

echo "=== PERMISOS ENCONTRADOS EN LA BD ===\n";
foreach ($permisos as $p) {
    echo "- $p\n";
}
