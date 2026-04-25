<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DiaDiem;
use App\Services\SerpApiService;

class UpdateCoordsCommand extends Command
{
    protected $signature = 'serp:update-coords
                            {--only-missing : Chỉ cập nhật địa điểm chưa có tọa độ}
                            {--limit=50 : Số địa điểm tối đa xử lý trong 1 lần chạy}
                            {--id= : Chỉ cập nhật 1 địa điểm theo ID}';

    protected $description = 'Lấy tọa độ GPS (kinh độ, vĩ độ) chính xác từ SerpApi Google Maps và cập nhật vào Database';

    private SerpApiService $serpApi;

    public function __construct(SerpApiService $serpApi)
    {
        parent::__construct();
        $this->serpApi = $serpApi;
    }

    public function handle()
    {
        $onlyMissing = $this->option('only-missing');
        $limit       = (int) $this->option('limit');
        $singleId    = $this->option('id');

        $query = DiaDiem::query();

        if ($singleId) {
            $query->where('id', $singleId);
        } elseif ($onlyMissing) {
            $query->where(function ($q) {
                $q->whereNull('vi_do')
                  ->orWhereNull('kinh_do')
                  ->orWhere('vi_do', '')
                  ->orWhere('kinh_do', '');
            });
        }

        $places = $query->limit($limit)->get();

        if ($places->isEmpty()) {
            $this->info('Không có địa điểm nào cần cập nhật tọa độ.');
            return 0;
        }

        $this->info("Tìm thấy {$places->count()} địa điểm. Bắt đầu lấy tọa độ từ Google Maps...");
        $this->newLine();

        $updated = 0;
        $failed  = 0;
        $tableData = [];

        foreach ($places as $place) {
            $this->line("🔍 Đang tìm: {$place->ten_dia_diem}...");

            // Tìm kiếm trên Google Maps với tên + địa chỉ để chính xác nhất
            $searchQuery = $place->ten_dia_diem . ' ' . ($place->dia_chi ?? 'Đà Nẵng');
            $result = $this->serpApi->getGoogleMapsPlace($place->ten_dia_diem, $place->dia_chi ?? 'Đà Nẵng');

            if ($result && $result['vi_do'] && $result['kinh_do']) {
                $oldLat = $place->vi_do;
                $oldLng = $place->kinh_do;

                $place->update([
                    'vi_do'   => $result['vi_do'],
                    'kinh_do' => $result['kinh_do'],
                    // Cập nhật thêm địa chỉ và đánh giá nếu trống
                    'dia_chi'             => $place->dia_chi ?: ($result['dia_chi'] ?? $place->dia_chi),
                    'danh_gia_trung_binh' => $place->danh_gia_trung_binh ?: ($result['danh_gia_trung_binh'] ?? $place->danh_gia_trung_binh),
                ]);

                $updated++;
                $tableData[] = [
                    $place->id,
                    $place->ten_dia_diem,
                    $result['vi_do'] . ', ' . $result['kinh_do'],
                    '✅ Cập nhật',
                ];
            } else {
                $failed++;
                $tableData[] = [
                    $place->id,
                    $place->ten_dia_diem,
                    ($place->vi_do ?? 'null') . ', ' . ($place->kinh_do ?? 'null'),
                    '❌ Không tìm thấy',
                ];
            }

            // Delay nhỏ để tránh rate limit SerpApi
            sleep(1);
        }

        $this->newLine();
        $this->table(
            ['ID', 'Tên địa điểm', 'Tọa độ GPS', 'Kết quả'],
            $tableData
        );

        $this->newLine();
        $this->info("✅ Cập nhật thành công: {$updated} địa điểm.");
        if ($failed > 0) {
            $this->warn("⚠️  Không tìm thấy tọa độ: {$failed} địa điểm.");
        }

        return 0;
    }
}
