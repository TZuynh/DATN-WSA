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
        Schema::create('bien_bang_nhan_xets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoi_dong_id')->constrained('hoi_dongs');
            $table->foreignId('dot_bao_cao_id')->constrained('dot_bao_caos');
            $table->text('noi_dung_bien_ban');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bien_bang_nhan_xets');
    }
};
