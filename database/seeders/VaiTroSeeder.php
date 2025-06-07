<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VaiTro;

class VaiTroSeeder extends Seeder
{
    public function run()
    {
        $vaiTros = [
            [
                'ten' => 'Chủ tịch hội đồng',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ten' => 'Giảng viên phản biện',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ten' => 'Giảng viên hướng dẫn',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
            'ten' => 'Giảng viên khác',
            'created_at' => now(),
            'updated_at' => now()
            ]
        ];

        foreach ($vaiTros as $vaiTro) {
            VaiTro::updateOrCreate(
                ['ten' => $vaiTro['ten']],
                $vaiTro
            );
        }
    }
}
