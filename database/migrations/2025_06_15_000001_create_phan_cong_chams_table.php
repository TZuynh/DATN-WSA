<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('phan_cong_chams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('de_tai_id')->constrained('de_tais')->onDelete('cascade');
            $table->foreignId('hoi_dong_id')->constrained('hoi_dongs')->onDelete('cascade');
            $table->dateTime('lich_cham')->nullable();
            $table->timestamps();

            // Đảm bảo mỗi đề tài chỉ được phân công một lần
            $table->unique(['de_tai_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('phan_cong_chams');
    }
}; 