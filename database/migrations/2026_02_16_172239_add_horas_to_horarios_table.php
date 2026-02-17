<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('horarios', function (Blueprint $table) {

            // Hora de entrada del turno
            $table->time('hora_entrada')
                ->after('nombre'); // cambia "nombre" por una columna real existente

            // Hora de salida del turno
            $table->time('hora_salida')
                ->after('hora_entrada');

            // Indica si el turno cruza medianoche
            $table->boolean('cruza_medianoche')
                ->default(false)
                ->after('hora_salida')
                ->comment('Indica si la salida ocurre al día siguiente');
        });
    }

    public function down(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            $table->dropColumn([
                'hora_entrada',
                'hora_salida',
                'cruza_medianoche',
            ]);
        });
    }
};
