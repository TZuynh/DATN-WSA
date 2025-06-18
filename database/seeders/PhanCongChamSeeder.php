<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PhanCongCham;
use App\Models\DeTai;
use App\Models\TaiKhoan;
use App\Models\SinhVien;
use App\Models\ChiTietNhom;

class PhanCongChamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy giảng viên ID 3
        $giangVien = TaiKhoan::where('id', 3)->where('vai_tro', 'giang_vien')->first();
        
        if (!$giangVien) {
            $this->command->error('Không tìm thấy giảng viên ID 3');
            return;
        }

        // Lấy giảng viên khác (không phải ID 3)
        $giangVienKhac = TaiKhoan::where('vai_tro', 'giang_vien')
            ->where('id', '!=', 3)
            ->first();
        
        if (!$giangVienKhac) {
            $this->command->error('Không tìm thấy giảng viên khác để phân công');
            return;
        }

        // Lấy đề tài đầu tiên có lịch chấm
        $deTai = DeTai::whereHas('lichCham')->first();
        
        if (!$deTai) {
            $this->command->error('Không tìm thấy đề tài nào có lịch chấm');
            return;
        }

        // Kiểm tra và gán sinh viên vào nhóm nếu chưa có
        $nhom = $deTai->nhom;
        if ($nhom) {
            $sinhVienChuaCoNhom = SinhVien::whereDoesntHave('chiTietNhom')->first();
            if ($sinhVienChuaCoNhom) {
                ChiTietNhom::create([
                    'nhom_id' => $nhom->id,
                    'sinh_vien_id' => $sinhVienChuaCoNhom->id,
                ]);
                $this->command->info('Đã gán sinh viên ' . $sinhVienChuaCoNhom->ten . ' vào nhóm: ' . $nhom->ten);
            }
        }

        // Kiểm tra xem đã có phân công chấm cho đề tài này chưa
        $phanCongCham = PhanCongCham::where('de_tai_id', $deTai->id)->first();
        
        if ($phanCongCham) {
            // Cập nhật phân công chấm hiện tại
            $phanCongCham->update([
                'giang_vien_phan_bien_id' => $giangVien->id,
                'giang_vien_khac_id' => $giangVienKhac->id,
            ]);
            $this->command->info('Đã cập nhật phân công chấm cho đề tài: ' . $deTai->ten_de_tai);
        } else {
            // Tạo phân công chấm mới
            PhanCongCham::create([
                'de_tai_id' => $deTai->id,
                'giang_vien_huong_dan_id' => $giangVienKhac->id, // Giảng viên hướng dẫn
                'giang_vien_phan_bien_id' => $giangVien->id, // Giảng viên phản biện (ID 3)
                'giang_vien_khac_id' => $giangVienKhac->id, // Giảng viên khác
                'ngay_phan_cong' => now(),
            ]);
            $this->command->info('Đã tạo phân công chấm cho đề tài: ' . $deTai->ten_de_tai);
        }
    }
} 