<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('de_tai_mau', function (Blueprint $table) {
            $table->id();
            $table->string('ten_de_tai');
            $table->text('mo_ta');
            $table->text('yeu_cau');
            $table->text('tai_lieu_tham_khao');
            $table->integer('so_luong_sinh_vien');
            $table->enum('trang_thai', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('de_tai_mau');
    }
}; 