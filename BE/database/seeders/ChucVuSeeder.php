<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChucVuSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $chucVus = [
            [
                'id' => 1,
                'ten_chuc_vu' => 'Super Admin',
                'slug_chuc_vu' => Str::slug('Super Admin'),
                'tinh_trang' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'ten_chuc_vu' => 'Nhân viên',
                'slug_chuc_vu' => Str::slug('Nhân viên'),
                'tinh_trang' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('chuc_vus')->upsert(
            $chucVus,
            ['id'],
            ['ten_chuc_vu', 'slug_chuc_vu', 'tinh_trang', 'updated_at']
        );
    }
}
