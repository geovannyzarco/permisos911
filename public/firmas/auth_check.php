<?php

// 1. Cargar el autoloader y el kernel de Laravel
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';

// 2. Manejar la solicitud a través del kernel web para cargar la sesión
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$kernel->handle($request);

// 3. Verificar si el usuario está autenticado
if (!auth()->check()) {
    // Si es una petición AJAX (como en save.php), devolvemos JSON
    if ($request->ajax() || (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json')) {
        header('Content-Type: application/json');
        die(json_encode(['success' => false, 'message' => 'Sesión expirada o no autorizada.']));
    }
    
    // Si es navegación normal, redirigir al login principal
    header("Location: /permisos/login");
    exit;
}

// 4. Verificar roles (super_admin o admin)
$user = auth()->user();
if (!$user->hasAnyRole(['super_admin', 'admin'])) {
    die("Acceso Denegado: No tienes permisos suficientes para acceder a esta herramienta.");
}
