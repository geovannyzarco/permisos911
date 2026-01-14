<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empleados', function (Blueprint $table) {

    $table->foreignId('departamento_id')
          ->nullable()
          ->after('id')
          ->constrained('departamentos')
          ->noActionOnDelete();

    $table->foreignId('municipio_id')
          ->nullable()
          ->after('departamento_id')
          ->constrained('municipios')
          ->noActionOnDelete();

    $table->foreignId('distrito_id')
          ->nullable()
          ->after('municipio_id')
          ->constrained('distritos')
          ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropForeign(['departamento_id']);
            $table->dropForeign(['municipio_id']);
            $table->dropForeign(['distrito_id']);

            $table->dropColumn([
                'departamento_id',
                'municipio_id',
                'distrito_id',
            ]);
        });
    }
};
