<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->foreignId('estado_civil_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('estado_civil')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropForeign(['estado_civil_id']);
            $table->dropColumn('estado_civil_id');
        });
    }
};
