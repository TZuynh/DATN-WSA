<?php

namespace App\Imports;

use App\Models\TaiKhoan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Throwable;
use Illuminate\Support\Facades\Log;

class TaiKhoanImport implements ToModel, WithHeadingRow, WithValidation, WithStartRow, SkipsOnError
{
    use SkipsErrors;

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * @param array $row
     * @return TaiKhoan|null
     */
    public function model(array $row)
    {
        if (empty($row['name']) || empty($row['email']) || empty($row['password']) || empty($row['role'])) {
            return null;
        }

        return new TaiKhoan([
            'ten' => trim($row['name']),
            'email' => trim($row['email']),
            'mat_khau' => bcrypt(trim($row['password'])),
            'vai_tro' => trim($row['role']),
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tai_khoans,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,giang_vien',
        ];
    }

    /**
     * @param Throwable $e
     */
    public function onError(Throwable $e)
    {
        Log::error('Import error on row: ' . $e->getMessage());
    }
}