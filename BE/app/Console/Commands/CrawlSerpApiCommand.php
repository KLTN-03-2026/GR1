<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DiaDiem;
use App\Services\SerpApiService;

class CrawlSerpApiCommand extends Command
{
    /**
     * Tên và tham số của câu lệnh.
     * Cú pháp: php artisan serp:crawl "Nhà hàng Đà Nẵng" --loai="Quán ăn"
     */
    protected $signature = 'serp:crawl {query : Cụm từ khóa tìm kiếm (ví dụ: "Nhà hàng Đà nẵng")} 
                            {--loai= : Phân loại (Ví dụ: "Quán ăn", "Biển", "Chùa")}';

    /**
     * The console command description.
     */
    protected $description = 'Sử dụng SerpApi để cào dữ liệu Google Maps và lưu thẳng vào Database DiaDiem';

    private SerpApiService $serpApi;

    public function __construct(SerpApiService $serpApi)
    {
        parent::__construct();
        $this->serpApi = $serpApi;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = $this->argument('query');
        $loaiDiaDiem  = $this->option('loai');

        if (!$loaiDiaDiem) {
            $this->error('Bạn phải cung cấp tham số phân loại. Ví dụ: --loai="Quán ăn"');
            return 1;
        }

        $this->info("Bắt đầu crawl thông tin bằng Google Maps API cho từ khóa: '{$query}'...");

        $results = $this->serpApi->crawlGoogleMaps($query);

        if (empty($results)) {
            $this->error("Không tìm thấy kết quả nào hoặc có lỗi từ SerpApi.");
            return 1;
        }

        $this->info("Tìm thấy " . count($results) . " địa điểm tiềm năng. Bắt đầu phân tích và lưu Database...");

        $insertedCount = 0;
        $skippedCount = 0;
        $tableData = [];

        foreach ($results as $index => $placeData) {
            $tenDiaDiem = $placeData['ten_dia_diem'];

            // Kiểm tra xem địa điểm có tồn tại chưa (Dựa vào Tên)
            $existing = DiaDiem::where('ten_dia_diem', 'LIKE', '%' . $tenDiaDiem . '%')->first();

            if ($existing) {
                $skippedCount++;
                $tableData[] = [
                    $index + 1,
                    $tenDiaDiem,
                    'Bỏ qua (Đã tồn tại)',
                ];
                continue;
            }

            // Tiến hành lưu
            try {
                // Tạo mới DiaDiem với loại được cung cấp
                $placeData['loai_dia_diem'] = $loaiDiaDiem;
                
                // Mặc định Random Giá vé nếu không có để giả lập
                $placeData['gia_ve'] = rand(0, 1) ? 0 : rand(20, 200) * 1000;
                
                // Múi giờ
                $placeData['gio_mo_cua'] = '0' . rand(6, 8) . ':00:00';
                $placeData['gio_dong_cua'] = rand(18, 22) . ':00:00';

                DiaDiem::create($placeData);

                $insertedCount++;
                $tableData[] = [
                    $index + 1,
                    $tenDiaDiem,
                    'Thành công (Đã thêm)',
                ];

            } catch (\Exception $e) {
                $skippedCount++;
                $tableData[] = [
                    $index + 1,
                    $tenDiaDiem,
                    'Lỗi (Exception)',
                ];
                $this->warn("Lỗi lưu DB '{$tenDiaDiem}': " . $e->getMessage());
            }
        }

        $this->table(['STT', 'Tên Địa Điểm', 'Trạng Thái'], $tableData);
        $this->info("Hoàn tất! Đã thêm mới {$insertedCount} địa điểm. Bỏ qua {$skippedCount} địa điểm.");

        return 0;
    }
}
