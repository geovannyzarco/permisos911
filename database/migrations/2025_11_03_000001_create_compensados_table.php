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
        Schema::create('compensados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('desde');
            $table->dateTime('hasta');
            $table->text('justificacion');
            $table->text('adjunto');
            $table->unsignedBigInteger('permiso_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compensados');
    }
};
