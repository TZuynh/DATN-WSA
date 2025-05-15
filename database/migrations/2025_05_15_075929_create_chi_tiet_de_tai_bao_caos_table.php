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
        Schema::create('chi_tiet_de_tai_bao_caos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dot_bao_cao_id')->constrained();
            $table->foreignId('de_tai_id')->constrained();
            $table->foreignId('hoi_dong_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_de_tai_bao_caos');
    }
};
