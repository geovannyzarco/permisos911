<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Agregar columna color en tabla unidades
        if (Schema::hasTable('unidades') && !Schema::hasColumn('unidades', 'color')) {
            Schema::table('unidades', function (Blueprint $table) {
                $table->string('color', 30)->nullable();
            });
        }

        // 2. Agregar columna color en tabla grupos
        if (Schema::hasTable('grupos') && !Schema::hasColumn('grupos', 'color')) {
            Schema::table('grupos', function (Blueprint $table) {
                $table->string('color', 30)->nullable();
            });
        }

        // 3. Renombrar el estado con ID 6 a 'RECHAZADO'
        DB::table('estados')
            ->where('id', 6)
            ->update([
                'nombre' => 'RECHAZADO',
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Quitar columna color de unidades
        if (Schema::hasTable('unidades') && Schema::hasColumn('unidades', 'color')) {
            Schema::table('unidades', function (Blueprint $table) {
                $table->dropColumn('color');
            });
        }

        // 2. Quitar columna color de grupos
        if (Schema::hasTable('grupos') && Schema::hasColumn('grupos', 'color')) {
            Schema::table('grupos', function (Blueprint $table) {
                $table->dropColumn('color');
            });
        }

        // 3. Restaurar el nombre original del estado ID 6 a 'FUERA DE LIMITE'
        DB::table('estados')
            ->where('id', 6)
            ->update([
                'nombre' => 'FUERA DE LIMITE',
                'updated_at' => now(),
            ]);
    }
};
