<?php

namespace App\Imports;

use App\Models\Nhom;
use App\Models\SinhVien;
use App\Models\ChiTietNhom;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Log;

class NhomImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        $maNhoms = [];
        foreach ($rows as $row) {
            $maNhom = '';
            $tenNhom = '';
            $mssvString = '';
            try {
                $maNhom = trim($row['ma_nhom']);
                $tenNhom = trim($row['ten']);
                $mssvString = trim($row['mssv']);

                // Kiểm tra mã nhóm trùng lặp trong file
                if (in_array($maNhom, $maNhoms)) {
                    throw new \Exception("Mã nhóm {$maNhom} bị trùng lặp trong file");
                }
                $maNhoms[] = $maNhom;

                // Kiểm tra mã nhóm đã tồn tại trong database
                if (Nhom::where('ma_nhom', $maNhom)->exists()) {
                    throw new \Exception("Mã nhóm {$maNhom} đã tồn tại trong hệ thống");
                }

                // Tạo nhóm mới
                $nhom = Nhom::create([
                    'ma_nhom' => $maNhom,
                    'ten' => $tenNhom,
                    'giang_vien_id' => auth()->id(),
                    'trang_thai' => 'hoat_dong'
                ]);

                // Lấy danh sách MSSV từ chuỗi
                $mssvs = array_map('trim', explode(',', $mssvString));
                $sinhVienIds = [];

                // Tìm ID của các sinh viên
                foreach ($mssvs as $mssv) {
                    if (empty($mssv)) continue; // Bỏ qua MSSV rỗng
                    $sinhVien = SinhVien::where('mssv', $mssv)->first();
                    if ($sinhVien) {
                        $sinhVienIds[] = $sinhVien->id;
                    } else {
                        throw new \Exception("Không tìm thấy sinh viên có MSSV: {$mssv}");
                    }
                }

                // Kiểm tra số lượng sinh viên
                if (count($sinhVienIds) < 2) {
                    throw new \Exception("Nhóm {$maNhom} phải có ít nhất 2 sinh viên");
                }

                // Thêm các sinh viên vào nhóm
                $nhom->sinhViens()->attach($sinhVienIds);
            } catch (\Exception $e) {
                Log::error('Lỗi khi import nhóm: ' . $e->getMessage());
                // Thêm thông tin dòng bị lỗi vào exception
                $lineNumber = $rows->search($row) + 2;
                throw new \Exception("Dòng " . $lineNumber . ": " . $e->getMessage());
            }
        }
    }

    public function rules(): array
    {
        return [
            '*.ma_nhom' => 'required|string',
            '*.ten' => 'required|string|max:255',
            '*.mssv' => 'required|string'
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.ma_nhom.required' => 'Mã nhóm không được để trống',
            '*.ten.required' => 'Tên nhóm không được để trống',
            '*.mssv.required' => 'Danh sách MSSV không được để trống'
        ];
    }
} 