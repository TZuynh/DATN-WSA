<?php

namespace App\Imports;

use App\Models\DeTaiMau;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DeTaiMauImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new DeTaiMau([
            'ten' => $row['ten_de_tai'],
        ]);
    }

    public function rules(): array
    {
        return [
            'ten_de_tai' => 'required|string|max:255',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'ten_de_tai.required' => 'Tên đề tài không được để trống',
            'ten_de_tai.string' => 'Tên đề tài phải là chuỗi ký tự',
            'ten_de_tai.max' => 'Tên đề tài không được vượt quá 255 ký tự',
        ];
    }
} 