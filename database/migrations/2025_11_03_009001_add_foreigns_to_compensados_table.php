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
        Schema::table('compensados', function (Blueprint $table) {
            $table
                ->foreign('permiso_id')
                ->references('id')
                ->on('permisos')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compensados', function (Blueprint $table) {
            $table->dropForeign(['permiso_id']);
        });
    }
};
