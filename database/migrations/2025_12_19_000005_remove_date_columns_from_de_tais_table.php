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
        Schema::table('de_tais', function (Blueprint $table) {
            if (Schema::hasColumn('de_tais', 'ngay_bat_dau')) {
                $table->dropColumn('ngay_bat_dau');
            }
            if (Schema::hasColumn('de_tais', 'ngay_ket_thuc')) {
                $table->dropColumn('ngay_ket_thuc');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('de_tais', function (Blueprint $table) {
            $table->date('ngay_bat_dau')->nullable();
            $table->date('ngay_ket_thuc')->nullable();
        });
    }
};
