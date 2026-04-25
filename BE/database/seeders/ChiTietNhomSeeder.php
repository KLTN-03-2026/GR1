<?php

namespace Database\Seeders;

use App\Models\ChiTietNhom;
use App\Models\NguoiDung;
use App\Models\NhomDuLich;
use Illuminate\Database\Seeder;

class ChiTietNhomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nguoiDung = NguoiDung::first();
        $nhomDuLich = NhomDuLich::first();

        if ($nguoiDung && $nhomDuLich) {
            ChiTietNhom::firstOrCreate([
                'id_nguoi_dung' => $nguoiDung->id,
                'id_nhom_du_lich' => $nhomDuLich->id,
            ], [
                'vai_tro' => 'truong_nhom',
            ]);
        }
    }
}
