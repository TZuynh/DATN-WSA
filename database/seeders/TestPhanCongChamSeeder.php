<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PhanCongCham;
use App\Models\DeTai;
use App\Models\LichCham;
use App\Models\TaiKhoan;

class TestPhanCongChamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy tất cả giảng viên
        $giangViens = TaiKhoan::where('vai_tro', 'giang_vien')->get();
        
        if ($giangViens->isEmpty()) {
            $this->command->error('Không có giảng viên nào trong hệ thống!');
            return;
        }

        // Lấy tất cả đề tài có lịch chấm
        $deTais = DeTai::whereHas('lichCham')->get();
        
        if ($deTais->isEmpty()) {
            $this->command->error('Không có đề tài nào có lịch chấm!');
            return;
        }

        $this->command->info('Bắt đầu tạo phân công chấm mẫu...');

        foreach ($deTais as $deTai) {
            // Lấy 2 giảng viên ngẫu nhiên cho mỗi đề tài
            $randomGiangViens = $giangViens->random(2);
            
            // Kiểm tra xem đã có phân công chấm cho đề tài này chưa
            $existingPhanCong = PhanCongCham::where('de_tai_id', $deTai->id)->first();
            
            if (!$existingPhanCong) {
                PhanCongCham::create([
                    'de_tai_id' => $deTai->id,
                    'giang_vien_huong_dan_id' => $randomGiangViens[0]->id,
                    'giang_vien_phan_bien_id' => $randomGiangViens[0]->id,
                    'giang_vien_khac_id' => $randomGiangViens[1]->id,
                    'lich_cham' => $deTai->lichCham->lich_tao ?? now(),
                ]);
                
                $this->command->info("Đã tạo phân công chấm cho đề tài: {$deTai->ten_de_tai}");
                $this->command->info("  - GV phản biện: {$randomGiangViens[0]->ten} (ID: {$randomGiangViens[0]->id})");
                $this->command->info("  - GV khác: {$randomGiangViens[1]->ten} (ID: {$randomGiangViens[1]->id})");
            } else {
                $this->command->info("Đề tài {$deTai->ten_de_tai} đã có phân công chấm (ID: {$existingPhanCong->id})");
            }
        }

        $this->command->info('Hoàn thành tạo phân công chấm mẫu!');
        
        // Hiển thị thống kê
        $totalPhanCong = PhanCongCham::count();
        $this->command->info("Tổng số phân công chấm: {$totalPhanCong}");
        
        foreach ($giangViens as $gv) {
            $phanCongCount = PhanCongCham::where(function($query) use ($gv) {
                $query->where('giang_vien_phan_bien_id', $gv->id)
                      ->orWhere('giang_vien_khac_id', $gv->id);
            })->count();
            
            $this->command->info("  - {$gv->ten} (ID: {$gv->id}): {$phanCongCount} phân công");
        }
    }
} 