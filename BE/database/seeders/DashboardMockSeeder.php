<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DashboardMockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Generate 50 Users (NguoiDung) over the entire year
        $userIds = [];
        $now = Carbon::now();
        $currentYear = $now->year;
        
        for ($i = 1; $i <= 50; $i++) {
            $randomDate = Carbon::create($currentYear, rand(1, 12), rand(1, 28), rand(8, 22), rand(0, 59), rand(0, 59));
            $id = DB::table('nguoi_dungs')->insertGetId([
                'ten' => 'Người Dùng Mock ' . $i,
                'email' => 'mockuser' . rand(1000, 99999) . '@example.com',
                'mat_khau' => Hash::make('password123'),
                'so_dien_thoai' => '09' . rand(10000000, 99999999),
                'anh_dai_dien' => null,
                'is_active' => rand(0, 10) > 1 ? 1 : 0, // 90% active
                'hash_active' => null,
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ]);
            $userIds[] = $id;
        }

        if (empty($userIds)) return;
        
        $placeIds = DB::table('dia_diems')->pluck('id')->toArray();

        // 2. Generate 120 ChuyenDi (Trips / Itineraries) over the entire year
        $tripIds = [];
        foreach (range(1, 120) as $i) {
            $tripCreatedDate = Carbon::create($currentYear, rand(1, 12), rand(1, 28), rand(8, 22), rand(0, 59), rand(0, 59));
            $days = rand(2, 5);
            $budget = rand(15, 100) * 100000; // 1.5M -> 10M
            
            $assignUser = $userIds[array_rand($userIds)];
            
            $tripId = DB::table('chuyen_dis')->insertGetId([
                'id_nguoi_dung' => $assignUser,
                'ten_chuyen_di' => 'Chuyến đi Đà Nẵng ' . $days . 'N' . ($days - 1) . 'Đ',
                'so_ngay' => $days,
                'so_nguoi' => rand(1, 4),
                'ngan_sach' => $budget,
                'ngay_bat_dau' => $tripCreatedDate->copy()->addDays(rand(5, 30))->format('Y-m-d'),
                'trang_thai' => rand(1, 10) > 8 ? 0 : 1, // 80% public
                'created_at' => $tripCreatedDate,
                'updated_at' => $tripCreatedDate,
            ]);
            $tripIds[] = ['id' => $tripId, 'created_at' => $tripCreatedDate, 'owner' => $assignUser];
            
            // Seed Places in Itinerary
            if (!empty($placeIds)) {
                $numPlaces = rand(2, 6);
                $selectedPlaces = array_rand(array_flip($placeIds), $numPlaces < count($placeIds) ? $numPlaces : count($placeIds));
                if (!is_array($selectedPlaces)) $selectedPlaces = [$selectedPlaces];
                
                foreach ($selectedPlaces as $index => $pid) {
                    DB::table('lich_trinh_dia_diems')->insert([
                        'id_chuyen_di' => $tripId,
                        'id_dia_diem' => $pid,
                        'thu_tu_tham_quan' => $index + 1,
                        'created_at' => $tripCreatedDate,
                        'updated_at' => $tripCreatedDate,
                    ]);
                }
            }
        }
        
        // 3. Generate Groups
        foreach (range(1, 20) as $i) {
            $randomTrip = $tripIds[array_rand($tripIds)];
            $groupId = DB::table('nhom_du_lichs')->insertGetId([
                'id_tao_nhom' => $randomTrip['owner'],
                'ten_nhom' => 'Nhóm phượt ' . rand(1000, 9999),
                'id_chuyen_di' => $randomTrip['id'],
                'created_at' => $randomTrip['created_at'],
                'updated_at' => $randomTrip['created_at'],
            ]);
            
            $membersCount = rand(3, 10);
            $selectedUsers = array_rand(array_flip($userIds), $membersCount);
            if (!is_array($selectedUsers)) $selectedUsers = [$selectedUsers];
            
            if (!in_array($randomTrip['owner'], $selectedUsers)) {
                $selectedUsers[] = $randomTrip['owner'];
            }
            
            foreach ($selectedUsers as $uid) {
                DB::table('chi_tiet_nhoms')->insert([
                    'id_nguoi_dung' => $uid,
                    'id_nhom_du_lich' => $groupId,
                    'vai_tro' => $uid == $randomTrip['owner'] ? 'truong_nhom' : 'thanh_vien',
                    'trang_thai' => 1,
                    'created_at' => $randomTrip['created_at'],
                    'updated_at' => $randomTrip['created_at'],
                ]);
            }
        }
    }
}
