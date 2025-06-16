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
        Schema::table('hoi_dongs', function (Blueprint $table) {
            $table->foreignId('phong_id')->nullable()->after('de_tai_id')->constrained('phongs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hoi_dongs', function (Blueprint $table) {
            $table->dropForeign(['phong_id']);
            $table->dropColumn('phong_id');
        });
    }
}; 