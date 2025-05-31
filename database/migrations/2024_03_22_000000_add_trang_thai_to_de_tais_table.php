<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('de_tais', function (Blueprint $table) {
            $table->string('trang_thai')->default('chua_bat_dau')->after('dot_bao_cao_id')
                ->comment('Trạng thái: chua_bat_dau, dang_dien_ra, da_ket_thuc, da_huy');
        });
    }

    public function down()
    {
        Schema::table('de_tais', function (Blueprint $table) {
            $table->dropColumn('trang_thai');
        });
    }
}; 