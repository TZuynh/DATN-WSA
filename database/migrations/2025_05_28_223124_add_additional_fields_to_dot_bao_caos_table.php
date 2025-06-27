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
        Schema::table('dot_bao_caos', function (Blueprint $table) {
            $table->string('trang_thai')->default('chua_bat_dau')->after('ngay_ket_thuc')
                ->comment('Trạng thái: chua_bat_dau, dang_dien_ra, da_ket_thuc, da_huy');
            $table->text('mo_ta')->nullable()->after('trang_thai')
                ->comment('Mô tả ngắn về đợt báo cáo');
            $table->integer('so_luong_hoi_dong')->default(0)->after('mo_ta')
                ->comment('Số lượng hội đồng trong đợt báo cáo');
            $table->integer('so_luong_nhom')->default(0)->after('so_luong_hoi_dong')
                ->comment('Số lượng nhóm sinh viên tham gia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dot_bao_caos', function (Blueprint $table) {
            $table->dropColumn([
                'trang_thai',
                'mo_ta',
                'so_luong_hoi_dong',
                'so_luong_de_tai',
                'so_luong_nhom'
            ]);
        });
    }
};
