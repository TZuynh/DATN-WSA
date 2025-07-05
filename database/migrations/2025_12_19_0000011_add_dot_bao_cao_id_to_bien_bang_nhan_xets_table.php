<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bien_bang_nhan_xets', function (Blueprint $table) {
            $table->foreignId('dot_bao_cao_id')->nullable()->constrained('dot_bao_caos');
        });
    }

    public function down(): void
    {
        Schema::table('bien_bang_nhan_xets', function (Blueprint $table) {
            $table->dropForeign(['dot_bao_cao_id']);
            $table->dropColumn('dot_bao_cao_id');
        });
    }
}; 