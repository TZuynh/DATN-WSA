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
        Schema::table('phan_cong_vai_tros', function (Blueprint $table) {
            $table->string('loai_giang_vien')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phan_cong_vai_tros', function (Blueprint $table) {
            $table->dropColumn('loai_giang_vien');
        });
    }
};
