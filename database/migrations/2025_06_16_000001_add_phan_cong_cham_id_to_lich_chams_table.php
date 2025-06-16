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
        Schema::table('lich_chams', function (Blueprint $table) {
            $table->foreignId('phan_cong_cham_id')->after('de_tai_id')->constrained('phan_cong_chams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lich_chams', function (Blueprint $table) {
            $table->dropForeign(['phan_cong_cham_id']);
            $table->dropColumn('phan_cong_cham_id');
        });
    }
}; 