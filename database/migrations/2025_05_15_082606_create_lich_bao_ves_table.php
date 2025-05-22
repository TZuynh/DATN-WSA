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
        Schema::create('lich_bao_ves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nhom_id')->constrained('nhoms');
            $table->foreignId('hoi_dong_id')->constrained('hoi_dongs');
            $table->dateTime('ngay_gio_bao_ve');
            $table->string('dia_diem')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_bao_ves');
    }
};
