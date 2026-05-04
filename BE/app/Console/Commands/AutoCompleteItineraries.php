<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ChuyenDi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoCompleteItineraries extends Command
{
    protected $signature   = 'itinerary:auto-complete';
    protected $description = 'Tự động chuyển lịch trình sang Hoàn thành khi đã qua ngày kết thúc';

    public function handle(): int
    {
        $today = Carbon::today();

        // Lấy tất cả lịch trình đang ở trạng thái 1 (Lên kế hoạch) hoặc 2 (Đang đi)
        // và có đủ ngay_bat_dau + so_ngay để tính được ngày kết thúc
        $chuyenDis = ChuyenDi::whereIn('trang_thai', [1, 2])
            ->whereNotNull('ngay_bat_dau')
            ->whereNotNull('so_ngay')
            ->get();

        $countCompleted = 0;
        $countOngoing = 0;

        foreach ($chuyenDis as $cd) {
            $ngayBatDau = Carbon::parse($cd->ngay_bat_dau);
            $ngayKetThuc = Carbon::parse($cd->ngay_bat_dau)->addDays($cd->so_ngay - 1);

            if ($today->gt($ngayKetThuc)) {
                // Đã qua ngày kết thúc -> Hoàn thành (3)
                if ($cd->trang_thai != 3) {
                    $cd->trang_thai = 3;
                    $cd->save();
                    $countCompleted++;
                }
            } elseif ($today->between($ngayBatDau, $ngayKetThuc)) {
                // Đang trong thời gian chuyến đi -> Đang đi (2)
                if ($cd->trang_thai == 1) { // Chỉ chuyển từ Lên kế hoạch -> Đang đi
                    $cd->trang_thai = 2;
                    $cd->save();
                    $countOngoing++;
                }
            }
        }

        $this->info("✅ Đã cập nhật {$countCompleted} lịch trình sang Hoàn thành, {$countOngoing} lịch trình sang Đang đi.");
        Log::info("[AutoComplete] Hoàn thành: {$countCompleted}, Đang đi: {$countOngoing}. Ngày: {$today->toDateString()}");

        return Command::SUCCESS;
    }
}
