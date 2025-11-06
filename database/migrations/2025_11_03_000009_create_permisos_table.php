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
        Schema::create('permisos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('fecha_creacion');
            $table->dateTime('desde');
            $table->dateTime('hasta');
            $table->text('motivo');
            $table->text('adjunto');
            $table->text('comentarios');
            $table->unsignedBigInteger('empleado_id');
            $table->unsignedBigInteger('tipo_permiso_id');
            $table->integer('id_estado_aprobacion_grupo');
            $table->integer('id_jefe_grupo');
            $table->dateTime('fecha_aprobacion');
            $table->integer('id_aprobacion_unidad');
            $table->integer('id_jefe_unidad');
            $table->dateTime('fecha_aprobacion_unidad');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permisos');
    }
};
