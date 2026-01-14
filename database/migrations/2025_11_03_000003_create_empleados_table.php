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
            $table->string('oni')->unique();
            $table->string('nombre');
            $table->text('foto')->nullable();
            $table->text('firma')->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->integer('codigo_huella')->nullable();

            $table->string('nombre_conyuge')->nullable();
            $table->integer('numero_hijos')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->string('genero')->nullable();
            $table->unsignedBigInteger('grupo_id');
            $table->unsignedBigInteger('categoria_id');
            $table->unsignedBigInteger('horario_id');
            $table->unsignedBigInteger('unidad_id');
            $table->unsignedBigInteger('nivel_id')->nullable();
            $table->unsignedBigInteger('estado_id');
            $table->text('nota')->nullable();
            $table->timestampsTz();
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
