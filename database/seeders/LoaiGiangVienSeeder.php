<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoaiGiangVienSeeder extends Seeder
{
    public function run()
    {
        // Lấy id vai trò 'Thành viên'
        $thanhVienId = DB::table('vai_tros')->where('ten', 'Thành viên')->value('id');

        DB::table('loai_giang_viens')->insert([
            [
                'vai_tro_id' => $thanhVienId,
                'ten' => 'Giảng Viên Hướng Dẫn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'vai_tro_id' => $thanhVienId,
                'ten' => 'Giảng Viên Phản Biện',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'vai_tro_id' => $thanhVienId,
                'ten' => 'Giảng Viên Khác',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 