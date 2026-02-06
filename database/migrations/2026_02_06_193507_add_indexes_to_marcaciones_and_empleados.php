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
        Schema::table('marcaciones', function (Blueprint $table) {
            $table->index(['codigo', 'marcacion'], 'idx_marcaciones_codigo_marcacion');
        });

        Schema::table('empleados', function (Blueprint $table) {
            $table->index('codigo_huella', 'idx_empleados_codigo_huella');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marcaciones', function (Blueprint $table) {
            $table->dropIndex('idx_marcaciones_codigo_marcacion');
        });

        Schema::table('empleados', function (Blueprint $table) {
            $table->dropIndex('idx_empleados_codigo_huella');
        });
    }
};
