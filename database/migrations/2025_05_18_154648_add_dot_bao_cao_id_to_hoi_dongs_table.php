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
            $table->foreignId('dot_bao_cao_id')->constrained('dot_bao_caos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hoi_dongs', function (Blueprint $table) {
            $table->dropForeign(['dot_bao_cao_id']);
            $table->dropColumn('dot_bao_cao_id');
        });
    }
};
