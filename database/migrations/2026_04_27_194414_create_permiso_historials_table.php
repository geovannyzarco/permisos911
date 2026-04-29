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
Schema::create('permiso_historials', function (Blueprint $table) {
        $table->id();

        // 🔗 referencia principal (opcional pero recomendable)
        $table->foreignId('permiso_id')->constrained()->cascadeOnDelete();

        // 🧠 tipo de evento (auditoría real)
        $table->string('tipo_evento');
        // Ej: CREACION, VB, APROBACION, EDICION

        // 👤 usuario que ejecuta
        $table->unsignedBigInteger('empleado_id');
        $table->string('empleado_oni');
        $table->string('empleado_nombre');

        // 🏢 estructura organizacional (snapshot)
        $table->unsignedBigInteger('division_id')->nullable();
        $table->string('division_nombre')->nullable();

        $table->unsignedBigInteger('unidad_id')->nullable();
        $table->string('unidad_nombre')->nullable();

        $table->unsignedBigInteger('grupo_id')->nullable();
        $table->string('grupo_nombre')->nullable();

        // 📄 datos del permiso (snapshot completo)
        $table->unsignedBigInteger('tipo_permiso_id');
        $table->string('tipo_permiso_nombre');

        $table->dateTime('desde');
        $table->dateTime('hasta');
        $table->decimal('duracion', 8, 2);

        $table->text('motivo')->nullable();
        $table->string('adjunto')->nullable();

        // 📊 estado
        $table->unsignedBigInteger('estado_id')->nullable();
        $table->string('estado_nombre')->nullable();

        // 💬 comentarios del aprobador
        $table->text('comentario')->nullable();

        // 🕓 auditoría
        $table->timestamp('fecha_evento'); // cuando ocurrió la acción

        // 🧾 metadatos técnicos (opcional pero PRO)
        $table->string('ip')->nullable();
        $table->text('user_agent')->nullable();

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permiso_historials');
    }
};
