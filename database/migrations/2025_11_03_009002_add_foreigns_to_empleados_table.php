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
        Schema::table('empleados', function (Blueprint $table) {
            $table
                ->foreign('grupo_id')
                ->references('id')
                ->on('grupos')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('categoria_id')
                ->references('id')
                ->on('categorias')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('horario_id')
                ->references('id')
                ->on('horarios')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('unidad_id')
                ->references('id')
                ->on('unidades')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('nivel_id')
                ->references('id')
                ->on('niveles')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('estado_id')
                ->references('id')
                ->on('estados')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropForeign(['grupo_id']);
            $table->dropForeign(['categoria_id']);
            $table->dropForeign(['horario_id']);
            $table->dropForeign(['unidad_id']);
            $table->dropForeign(['nivel_id']);
            $table->dropForeign(['estado_id']);
        });
    }
};
