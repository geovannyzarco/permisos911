<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('permisos', function (Blueprint $table) {
            $table->string('cargo_funcional')->nullable()->comment('Cargo funcional registrado en el permiso');
            $table->string('descripcion_funcion')->nullable()->comment('Descripción de la función registrada en el permiso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permisos', function (Blueprint $table) {
            $table->dropColumn(['cargo_funcional', 'descripcion_funcion']);
        });
    }
};
