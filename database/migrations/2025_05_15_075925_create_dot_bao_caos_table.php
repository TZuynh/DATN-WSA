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
        Schema::create('dot_bao_caos', function (Blueprint $table) {
            $table->id();
            $table->year('nam_hoc');
            $table->unsignedBigInteger('hoc_ky_id');
            $table->timestamps();
            $table->foreign('hoc_ky_id')->references('id')->on('hoc_kys')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dot_bao_caos');
    }
};
