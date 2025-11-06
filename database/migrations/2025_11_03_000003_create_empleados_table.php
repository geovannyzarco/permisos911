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
        Schema::create('empleados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('oni');
            $table->string('nombre');
            $table->text('foto');
            $table->text('firma');
            $table->unsignedBigInteger('grupo_id');
            $table->unsignedBigInteger('categoria_id');
            $table->unsignedBigInteger('horario_id');
            $table->unsignedBigInteger('unidad_id');
            $table->unsignedBigInteger('nivel_id');
            $table->unsignedBigInteger('estado_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
