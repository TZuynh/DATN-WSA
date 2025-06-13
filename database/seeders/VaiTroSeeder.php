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
                'ten' => 'Trưởng tiểu ban',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ten' => 'Thư ký',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ten' => 'Thành viên',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        foreach ($vaiTros as $vaiTro) {
            VaiTro::updateOrCreate(
                ['ten' => $vaiTro['ten']],
                $vaiTro
            );
        }
    }
}
