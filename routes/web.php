<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermisoPdfController;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/permiso/{id}/pdf', [PermisoPdfController::class, 'generar'])
    ->name('permiso.pdf');

Route::get('/descargar/{path}', function ($path) {
    if (!Storage::disk('public')->exists($path)) {
        abort(404, 'Archivo no encontrado');
    }
    return Storage::disk('public')->download($path);
})->where('path', '.*')->name('descargar.archivo');
