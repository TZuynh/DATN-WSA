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
        Schema::create('bang_diems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('giang_vien_id')->constrained('tai_khoans');
            $table->foreignId('sinh_vien_id')->constrained();
            $table->foreignId('dot_bao_cao_id')->constrained();
            $table->decimal('diem_bao_cao', 5, 2)->nullable();
            $table->decimal('diem_thuyet_trinh', 5, 2)->nullable();
            $table->decimal('diem_demo', 5, 2)->nullable();
            $table->decimal('diem_cau_hoi', 5, 2)->nullable();
            $table->decimal('diem_cong', 5, 2)->nullable();
            $table->text('binh_luan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bang_diems');
    }
};
