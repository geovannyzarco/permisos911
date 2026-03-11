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
        Schema::table('empleados', function (Blueprint $table) {
 // Permiso de arma
            $table->boolean('permiso_portacion_arma')
                ->default(false)
                ->after('telefono');

            $table->string('numero_permiso_arma')
                ->nullable()
                ->after('permiso_portacion_arma');


            // Licencia de conducir
            $table->boolean('licencia_conducir')
                ->default(false)
                ->after('numero_permiso_arma');

            $table->enum('tipo_licencia', ['liviana','pesada','particular'])
                ->nullable()
                ->after('licencia_conducir');

            $table->string('numero_licencia')
                ->nullable()
                ->after('tipo_licencia');


            // Licencia para motocicleta
            $table->boolean('licencia_moto')
                ->default(false)
                ->after('numero_licencia');

            $table->string('numero_licencia_moto')
                ->nullable()
                ->after('licencia_moto');


            // Permiso para estudiar
            $table->boolean('permiso_estudio')
                ->default(false)
                ->after('numero_licencia_moto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropColumn([
                'permiso_portacion_arma',
                'numero_permiso_arma',
                'licencia_conducir',
                'tipo_licencia',
                'numero_licencia',
                'licencia_moto',
                'numero_licencia_moto',
                'permiso_estudio',
            ]);
        });
    }
};
