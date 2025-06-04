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
        Schema::create('de_tais', function (Blueprint $table) {
            $table->id();
            $table->string('ma_de_tai')->unique();
            $table->foreignId('de_tai_mau_id')->nullable()->constrained('de_tai_maus');
            $table->text('mo_ta')->nullable();
            $table->date('ngay_bat_dau')->nullable();
            $table->date('ngay_ket_thuc')->nullable();
            $table->foreignId('nhom_id')->nullable()->constrained('nhoms');
            $table->foreignId('giang_vien_id')->nullable()->constrained('tai_khoans');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('de_tais');
    }
};
