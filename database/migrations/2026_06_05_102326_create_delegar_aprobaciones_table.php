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
        Schema::create('delegar_aprobaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jefe_id')->constrained('empleados')->onDelete('cascade');
            $table->string('tipo_delegacion'); // 'grupo' o 'unidad'
            $table->unsignedBigInteger('entidad_delegada_id'); // ID de la unidad o grupo delegado
            $table->date('fecha_desde')->nullable();
            $table->date('fecha_hasta')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delegar_aprobaciones');
    }
};
