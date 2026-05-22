<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('permisos', function (Blueprint $table) {
            $table->string('id_oni_jefe_division')->nullable()->after('id_jefe_aprobacion');
            $table->dateTimeTz('fecha_aprobacion_jefe_division')->nullable()->after('id_oni_jefe_division');
            $table->integer('id_estado_aprobacion_jefe_division')->nullable()->after('fecha_aprobacion_jefe_division');
            $table->boolean('tramitado')->default(false)->after('id_estado_aprobacion_jefe_division');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permisos', function (Blueprint $table) {
            $table->dropColumn([
                'id_oni_jefe_division',
                'fecha_aprobacion_jefe_division',
                'id_estado_aprobacion_jefe_division',
                'tramitado',
            ]);
        });
    }
};
