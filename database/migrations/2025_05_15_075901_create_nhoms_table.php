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
        Schema::create('nhoms', function (Blueprint $table) {
            $table->id();
            $table->string('ma_nhom')->unique();
            $table->string('ten');
            $table->foreignId('giang_vien_id')->constrained('tai_khoans'); // giáo viên hướng dẫn
            $table->foreignId('sinh_vien_id')->nullable()->constrained('sinh_viens');
            $table->enum('trang_thai', ['hoat_dong', 'khong_hoat_dong'])->default('hoat_dong');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nhoms');
    }
};
