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
            $table->foreignId('de_tai_id')->nullable()->after('dot_bao_cao_id')
                ->constrained('de_tais')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hoi_dongs', function (Blueprint $table) {
            $table->dropForeign(['de_tai_id']);
            $table->dropColumn('de_tai_id');
        });
    }
}; 