<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grupos', function (Blueprint $table) {

            if (! Schema::hasColumn('grupos', 'unidad_id')) {

                $table->unsignedBigInteger('unidad_id')->nullable()->after('id');

                $table->index('unidad_id');

                $table->foreign('unidad_id')
                    ->references('id')
                    ->on('unidades')
                    ->noActionOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            $table->dropForeign(['unidad_id']);
            $table->dropIndex(['unidad_id']);
            $table->dropColumn('unidad_id');
        });
    }
};
