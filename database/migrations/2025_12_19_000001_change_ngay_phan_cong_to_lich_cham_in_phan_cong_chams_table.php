<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('phan_cong_chams', function (Blueprint $table) {
            $table->dropColumn('ngay_phan_cong');
            $table->datetime('lich_cham')->after('giang_vien_khac_id');
        });
    }

    public function down()
    {
        Schema::table('phan_cong_chams', function (Blueprint $table) {
            $table->dropColumn('lich_cham');
            $table->date('ngay_phan_cong')->after('giang_vien_khac_id');
        });
    }
}; 