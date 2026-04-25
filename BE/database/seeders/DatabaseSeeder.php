<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            ChucVuSeeder::class,
            PhanQuyenSeeder::class,
            NguoiDungSeeder::class,
            AdminSeeder::class,
            AmThucSeeder::class,
            CheckInSeeder::class,
            GiaiTriSeeder::class,
            TamLinhSeeder::class,
            ThemDiaDiemSeeder::class,
            DummyDataSeeder::class,
            DashboardMockSeeder::class,
            DiaDiemYeuThichSeeder::class,
            DanhGiaHeThongSeeder::class,
        ]);
    }
}
