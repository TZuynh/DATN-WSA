<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaiKhoan;
use Illuminate\Support\Facades\Hash;
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        TaiKhoan::firstOrCreate(
            ['email' => 'admin@caothang.edu.vn'],
            [
                'ten' => 'Quản trị viên',
                'mat_khau' => Hash::make('Admin@123'),
                'vai_tro' => 'admin',
            ]
        );
    }
}
