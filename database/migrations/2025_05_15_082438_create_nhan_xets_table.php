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
        Schema::create('nhan_xets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bang_diem_id')->constrained('bang_diems');
            $table->foreignId('tai_khoan_id')->constrained('tai_khoans'); // GV đưa nhận xét
            $table->text('noi_dung_nhan_xet');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nhan_xets');
    }
};
