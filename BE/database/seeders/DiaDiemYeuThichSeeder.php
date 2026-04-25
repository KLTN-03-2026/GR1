<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiaDiemYeuThichSeeder extends Seeder
{
    /**
     * Seed dữ liệu yêu thích địa điểm.
     * - Lấy hết người dùng và địa điểm hiện có từ DB.
     * - Phân bổ có trọng số: địa điểm đầu danh sách (nổi tiếng hơn) được nhiều lượt yêu thích hơn.
     * - Tôn trọng ràng buộc UNIQUE (id_nguoi_dung, id_dia_diem).
     */
    public function run(): void
    {
        // Xoá dữ liệu cũ nếu muốn chạy lại
        DB::table('dia_diem_yeu_thich')->delete();

        $userIds   = DB::table('nguoi_dungs')->pluck('id')->toArray();
        $placeIds  = DB::table('dia_diems')->pluck('id')->toArray();

        if (empty($userIds) || empty($placeIds)) {
            $this->command->warn('⚠  Không có người dùng hoặc địa điểm nào trong DB. Hãy chạy seeder tương ứng trước.');
            return;
        }

        $totalUsers  = count($userIds);
        $totalPlaces = count($placeIds);

        // ── Trọng số yêu thích theo vị trí địa điểm ──────────────────────────
        // Địa điểm đầu danh sách (index nhỏ) → trọng số cao → nhiều lượt yêu thích hơn.
        // Công thức: weight[i] = max(1, totalPlaces - i*0.8)  → giảm dần nhẹ.
        $weights = [];
        foreach ($placeIds as $i => $pid) {
            $weights[$pid] = max(1, $totalPlaces - $i * 0.8);
        }
        $totalWeight = array_sum($weights);

        $inserted = [];   // theo dõi các cặp đã insert: "userId_placeId"
        $rows     = [];

        // ── Tính số lượt yêu thích mục tiêu cho mỗi địa điểm ─────────────────
        // Mục tiêu tổng lượt: ~60% người dùng × số địa điểm (trung bình 1 người thích nhiều nơi)
        $targetTotal = (int) ($totalUsers * $totalPlaces * 0.55);
        $targetTotal = max($targetTotal, 80); // ít nhất 80 bản ghi

        foreach ($placeIds as $pid) {
            // Số lượt yêu thích cho địa điểm này (tính theo trọng số)
            $quota = (int) round(($weights[$pid] / $totalWeight) * $targetTotal);
            $quota = min($quota, $totalUsers); // không thể vượt tổng số người dùng

            // Shuffle users để random
            $shuffled = $userIds;
            shuffle($shuffled);

            $added = 0;
            foreach ($shuffled as $uid) {
                if ($added >= $quota) break;
                $key = "{$uid}_{$pid}";
                if (isset($inserted[$key])) continue;

                $inserted[$key] = true;
                $rows[] = [
                    'id_nguoi_dung' => $uid,
                    'id_dia_diem'   => $pid,
                    'created_at'    => Carbon::now()->subDays(rand(0, 180))->subHours(rand(0, 23)),
                    'updated_at'    => Carbon::now(),
                ];
                $added++;
            }
        }

        // Insert theo batch để tránh quá tải
        $chunks = array_chunk($rows, 200);
        foreach ($chunks as $chunk) {
            DB::table('dia_diem_yeu_thich')->insert($chunk);
        }

        $this->command->info("✅ Đã seed " . count($rows) . " lượt yêu thích cho " . $totalPlaces . " địa điểm từ " . $totalUsers . " người dùng.");
    }
}
