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
        Schema::create('phan_cong_vai_tros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoi_dong_id')->constrained();
            $table->foreignId('tai_khoan_id')->constrained('tai_khoans');
            $table->foreignId('vai_tro_id')->constrained('vai_tros'); // Vai trò trong hội đồng
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phan_cong_vai_tros');
    }
};
