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
        Schema::create('bao_cao_qua_trinhs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nhom_id')->constrained('nhoms');
            $table->foreignId('dot_bao_cao_id')->constrained('dot_bao_caos');
            $table->text('noi_dung_bao_cao');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bao_cao_qua_trinhs');
    }
};
