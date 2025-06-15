<?php

namespace App\Imports;

use App\Models\Phong;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class PhongImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Kiểm tra cả hai tên cột có thể có
        $tenPhong = $row['ten_phong'] ?? $row['Tên phòng'] ?? null;

        if ($tenPhong) {
            return new Phong([
                'ten_phong' => $tenPhong,
            ]);
        }

        return null;
    }

    public function rules(): array
    {
        return [
            'ten_phong' => 'required|string|max:255|unique:phongs,ten_phong',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'ten_phong.required' => 'Tên phòng không được để trống',
            'ten_phong.unique' => 'Tên phòng đã tồn tại',
        ];
    }
} 