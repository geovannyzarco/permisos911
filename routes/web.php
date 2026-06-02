<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermisoPdfController;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\MarcacionImportController;

Route::redirect('/', '/permisos');

Route::get('/permiso/{id}/pdf', [PermisoPdfController::class, 'generar'])
    ->name('permiso.pdf');

Route::get('/descargar/{path}', function ($path) {
    if (!Storage::disk('public')->exists($path)) {
        abort(404, 'Archivo no encontrado');
    }
    return Storage::disk('public')->download($path);
})->where('path', '.*')->name('descargar.archivo');

Route::post('/marcaciones/import', [MarcacionImportController::class, 'import'])
    ->name('marcaciones.import');

Route::get('/foto-empleado/{filename}', function ($filename) {

    $path = storage_path('app/private/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
})->where('filename', '.*')->name('foto.empleado');
