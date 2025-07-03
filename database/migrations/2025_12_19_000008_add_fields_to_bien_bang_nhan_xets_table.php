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
        Schema::table('bien_bang_nhan_xets', function (Blueprint $table) {
            $table->text('hinh_thuc')->nullable();
            $table->text('cap_thiet')->nullable();
            $table->text('muc_tieu')->nullable();
            $table->text('tai_lieu')->nullable();
            $table->text('phuong_phap')->nullable();
            $table->text('ket_qua')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bien_bang_nhan_xets', function (Blueprint $table) {
            $table->dropColumn(['hinh_thuc', 'cap_thiet', 'muc_tieu', 'tai_lieu', 'phuong_phap', 'ket_qua']);
        });
    }
}; 