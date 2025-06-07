<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('de_tais', function (Blueprint $table) {
            $table->string('ten_de_tai')->after('ma_de_tai');
            $table->text('y_kien_giang_vien')->nullable()->after('mo_ta');
            $table->foreignId('vai_tro_id')->nullable()->constrained('vai_tros')->after('trang_thai');
            $table->tinyInteger('trang_thai')->default(0)->comment('0=đang thực hiện, 1=giáo viên đồng ý báo cáo, 2=giáo viên phản biện đồng ý báo cáo, 3=không được giáo viên HD đồng ý, 4=không được GVPB đồng ý');
        });
    }

    public function down(): void
    {
        Schema::table('de_tais', function (Blueprint $table) {
            $table->foreignId('de_tai_mau_id')->nullable()->constrained('de_tai_maus');
            $table->dropColumn(['ten_de_tai', 'y_kien_giang_vien', 'the_de_tai', 'trang_thai']);
        });
    }
};
