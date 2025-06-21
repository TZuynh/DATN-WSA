<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HocKySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('hoc_kys')->insert([
            ['ten' => 'Học kỳ 1', 'created_at' => now(), 'updated_at' => now()],
            ['ten' => 'Học kỳ 2', 'created_at' => now(), 'updated_at' => now()],
            ['ten' => 'Học kỳ 3', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
} 