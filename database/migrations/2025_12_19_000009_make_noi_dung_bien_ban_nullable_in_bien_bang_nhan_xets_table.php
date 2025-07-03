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
            $table->text('noi_dung_bien_ban')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bien_bang_nhan_xets', function (Blueprint $table) {
            $table->text('noi_dung_bien_ban')->nullable(false)->change();
        });
    }
}; 