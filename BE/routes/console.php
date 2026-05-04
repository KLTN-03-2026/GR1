<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\DiaDiem;
use App\Services\SerpApiService;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Artisan command: php artisan serp:update-images
 *
 * Tự động lấy ảnh thật từ Google Images (via SerpApi)
 * cho tất cả địa điểm trong database.
 *
 * Options:
 *  --id=1        Chỉ update 1 địa điểm (theo ID)
 *  --limit=10    Giới hạn số lượng địa điểm
 *  --force       Update cả những địa điểm đã có ảnh
 */
Artisan::command('serp:update-images {--id= : ID địa điểm cụ thể} {--limit= : Giới hạn số lượng} {--force : Update cả những địa điểm đã có ảnh}', function () {
    /** @var \Illuminate\Console\Command $this */
    $serp = new SerpApiService();

    // Kiểm tra account
    $this->info('🔍 Kiểm tra tài khoản SerpApi...');
    $account = $serp->getAccountInfo();
    $this->line("   Plan: {$account['plan']} | Searches còn lại: {$account['searches_remaining']}");

    if ($account['searches_remaining'] === 0) {
        $this->error('❌ Hết lượt search SerpApi. Vui lòng nâng cấp gói hoặc đợi tháng sau.');
        return;
    }

    // Build query
    $query = DiaDiem::query();

    if ($this->option('id')) {
        $query->where('id', (int)$this->option('id'));
    } elseif (!$this->option('force')) {
        // Chỉ lấy những địa điểm chưa có ảnh hoặc ảnh từ Unsplash (placeholder)
        $query->where(function ($q) {
            $q->whereNull('image')
              ->orWhere('image', '')
              ->orWhere('image', 'like', '%unsplash.com%');
        });
    }

    if ($this->option('limit')) {
        $query->limit((int)$this->option('limit'));
    }

    $places = $query->get();

    if ($places->isEmpty()) {
        $this->info('✅ Tất cả địa điểm đã có ảnh thật. Dùng --force để update lại.');
        return;
    }

    $total   = $places->count();
    $success = 0;
    $failed  = 0;

    $this->info("📸 Bắt đầu fetch ảnh cho {$total} địa điểm...\n");
    $bar = $this->output->createProgressBar($total);
    $bar->start();

    foreach ($places as $place) {
        $bar->advance();
        $searchQuery = $place->ten_dia_diem . ' ' . ($place->dia_chi ?? 'Đà Nẵng');
        $imageUrl = $serp->getGoogleImage($place->ten_dia_diem, $place->dia_chi ?? 'Đà Nẵng Việt Nam');

        if ($imageUrl) {
            $place->update(['image' => $imageUrl]);
            $success++;
        } else {
            $failed++;
        }

        // Tránh rate limit – nghỉ 1 giây giữa các request
        sleep(1);
    }

    $bar->finish();
    $this->newLine(2);
    $this->info("✅ Hoàn tất! Thành công: {$success} | Thất bại: {$failed}");
    $this->line("💡 Mẹo: Chạy với --force để update lại tất cả địa điểm (kể cả đã có ảnh).");
})->purpose('Tự động lấy ảnh thật từ Google Images (SerpApi) cho tất cả địa điểm');


/**
 * Artisan command: php artisan serp:update-details
 *
 * Tự động lấy thông tin chi tiết từ Google Maps (via SerpApi):
 * rating, tọa độ GPS, địa chỉ thực tế cho từng địa điểm.
 */
Artisan::command('serp:update-details {--id= : ID địa điểm cụ thể} {--limit= : Giới hạn số lượng}', function () {
    /** @var \Illuminate\Console\Command $this */
    $serp = new SerpApiService();

    $this->info('🗺️  Bắt đầu fetch thông tin từ Google Maps (SerpApi)...');

    $query = DiaDiem::query();

    if ($this->option('id')) {
        $query->where('id', (int)$this->option('id'));
    }

    if ($this->option('limit')) {
        $query->limit((int)$this->option('limit'));
    }

    $places = $query->get();
    $total = $places->count();
    $success = 0;

    $this->info("📍 Xử lý {$total} địa điểm...\n");
    $bar = $this->output->createProgressBar($total);
    $bar->start();

    foreach ($places as $place) {
        $bar->advance();
        $data = $serp->getGoogleMapsPlace($place->ten_dia_diem, $place->dia_chi ?? 'Đà Nẵng');

        if ($data) {
            $updates = array_filter([
                'danh_gia_trung_binh' => $data['danh_gia_trung_binh'],
                // Chỉ update tọa độ nếu địa điểm chưa có
                'vi_do'   => ($place->vi_do == 0 || !$place->vi_do)  ? $data['vi_do']   : null,
                'kinh_do' => ($place->kinh_do == 0 || !$place->kinh_do) ? $data['kinh_do'] : null,
                // Chỉ update ảnh nếu chưa có
                'image' => (!$place->image || str_contains($place->image ?? '', 'unsplash')) ? $data['image'] : null,
            ], fn($v) => $v !== null);

            if (!empty($updates)) {
                $place->update($updates);
                $success++;
            }
        }

        sleep(1);
    }

    $bar->finish();
    $this->newLine(2);
    $this->info("✅ Hoàn tất! Đã cập nhật {$success}/{$total} địa điểm.");
})->purpose('Tự động lấy thông tin chi tiết từ Google Maps (SerpApi) cho địa điểm');

Schedule::command('itinerary:auto-complete')
    ->dailyAt('00:05')
    ->withoutOverlapping()
    ->runInBackground();

