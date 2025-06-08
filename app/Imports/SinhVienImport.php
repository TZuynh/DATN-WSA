<?php

namespace App\Imports;

use App\Models\SinhVien;
use App\Models\Lop;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SinhVienImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Tìm hoặc tạo lớp mới
        $lop = Lop::firstOrCreate(
            ['ten_lop' => $row['lop']],
            ['ten_lop' => $row['lop']]
        );

        return new SinhVien([
            'mssv' => $row['mssv'],
            'ten' => $row['ten'],
            'lop_id' => $lop->id,
        ]);
    }

    public function rules(): array
    {
        return [
            'mssv' => 'required|string|regex:/^0306\\d{6}$/|unique:sinh_viens,mssv',
            'ten' => 'required|string|max:255',
            'lop' => 'required|string|max:50',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'mssv.required' => 'Mã số sinh viên không được để trống',
            'mssv.unique' => 'Mã số sinh viên đã tồn tại',
            'mssv.regex' => 'Mã số sinh viên phải bắt đầu bằng 0306 và có đủ 10 chữ số.',
            'ten.required' => 'Tên sinh viên không được để trống',
            'lop.required' => 'Tên lớp không được để trống',
            'lop.max' => 'Tên lớp không được vượt quá 50 ký tự',
        ];
    }
} 