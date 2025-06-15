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
            $table->foreignId('giang_vien_huong_dan_id')->constrained('tai_khoans')->onDelete('cascade');
            $table->foreignId('giang_vien_phan_bien_id')->constrained('tai_khoans')->onDelete('cascade');
            $table->foreignId('giang_vien_khac_id')->constrained('tai_khoans')->onDelete('cascade');
            $table->date('ngay_phan_cong');
            $table->timestamps();

            // Đảm bảo mỗi đề tài chỉ được phân công một lần trong một đợt báo cáo
            $table->unique(['de_tai_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('phan_cong_chams');
    }
}; 