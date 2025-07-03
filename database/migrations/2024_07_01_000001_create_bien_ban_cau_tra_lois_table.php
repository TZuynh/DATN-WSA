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
        Schema::create('bien_ban_cau_tra_lois', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bien_ban_nhan_xet_id')->constrained('bien_bang_nhan_xets')->onDelete('cascade');
            $table->text('cau_hoi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bien_ban_cau_tra_lois');
    }
}; 