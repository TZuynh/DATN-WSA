<?php

namespace App\Imports;

use App\Models\Lop;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LopImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Kiểm tra và lấy giá trị từ cột "Tên lớp" hoặc "ten_lop"
        $tenLop = $row['Tên lớp'] ?? $row['ten_lop'] ?? null;

        return new Lop([
            'ten_lop' => $tenLop,
        ]);
    }

    public function rules(): array
    {
        return [
            'ten_lop' => 'required|string|max:50|unique:lops,ten_lop',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'ten_lop.required' => 'Tên lớp không được để trống',
            'ten_lop.max' => 'Tên lớp không được vượt quá 50 ký tự',
            'ten_lop.unique' => 'Tên lớp đã tồn tại trong hệ thống',
            'Tên lớp.required' => 'Tên lớp không được để trống',
            'Tên lớp.max' => 'Tên lớp không được vượt quá 50 ký tự',
            'Tên lớp.unique' => 'Tên lớp đã tồn tại trong hệ thống',
        ];
    }
} 