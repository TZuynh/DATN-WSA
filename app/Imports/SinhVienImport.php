<?php

namespace App\Imports;

use App\Models\SinhVien;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SinhVienImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new SinhVien([
            'mssv' => $row['mssv'],
            'ten' => $row['ten'],
            'lop' => $row['lop'] ?? null,
            'nganh' => $row['nganh'] ?? null,
            'khoa_hoc' => $row['khoa_hoc'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'mssv' => 'required|string|regex:/^0306\\d{6}$/|unique:sinh_viens,mssv',
            'ten' => 'required|string|max:255',
            'lop' => 'nullable|string|max:50',
            'nganh' => 'nullable|string|max:100',
            'khoa_hoc' => 'nullable|string|max:20',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'mssv.required' => 'Mã số sinh viên không được để trống',
            'mssv.unique' => 'Mã số sinh viên đã tồn tại',
            'mssv.regex' => 'Mã số sinh viên phải bắt đầu bằng 0306 và có đủ 10 chữ số.',
            'ten.required' => 'Tên sinh viên không được để trống',
            'lop.max' => 'Tên lớp không được vượt quá 50 ký tự',
            'nganh.max' => 'Tên ngành không được vượt quá 100 ký tự',
            'khoa_hoc.max' => 'Khóa học không được vượt quá 20 ký tự',
        ];
    }
} 