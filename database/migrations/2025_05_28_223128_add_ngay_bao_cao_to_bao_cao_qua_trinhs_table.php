<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bao_cao_qua_trinhs', function (Blueprint $table) {
            $table->date('ngay_bao_cao')->after('noi_dung_bao_cao');
        });
    }

    public function down(): void
    {
        Schema::table('bao_cao_qua_trinhs', function (Blueprint $table) {
            $table->dropColumn('ngay_bao_cao');
        });
    }
}; 