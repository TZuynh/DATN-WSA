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
        Schema::create('thanh_vien_hoi_dongs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoi_dong_id')->constrained();
            $table->foreignId('tai_khoan_id')->constrained('tai_khoans');
            $table->enum('vai_tro', ['main', 'reviewer'])->default('main'); // phân biệt phản biện
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thanh_vien_hoi_dongs');
    }
};
