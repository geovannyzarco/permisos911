<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermisoPdfController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/permiso/{id}/pdf', [PermisoPdfController::class, 'generar'])
    ->name('permiso.pdf');
