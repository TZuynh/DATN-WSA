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
        Schema::create('lich_chams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nhom_id')->constrained();
            $table->foreignId('hoi_dong_id')->constrained();
            $table->foreignId('dot_bao_cao_id')->constrained();
            $table->dateTime('lich_tao');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_chams');
    }
};
