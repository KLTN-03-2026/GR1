<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SerpApiService;
use App\Models\DiaDiem;
use App\Models\DanhGia;
use App\Models\HinhAnhDiaDiem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SerpApiController extends Controller
{
    private $serp;

    public function __construct(SerpApiService $serp)
    {
        $this->serp = $serp;
    }

    public function accountInfo()
    {
        return response()->json([
            'status' => true,
            'data'   => $this->serp->getAccountInfo()
        ]);
    }

    public function updateImages(Request $request)
    {
        $ids   = $request->input('ids', []);
        $force = $request->boolean('force', false);
        $limit = $request->input('limit', 10);

        $query = DiaDiem::query();

        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        } elseif (!$force) {
            $query->where(function ($q) {
                $q->whereDoesntHave('gallery')
                  ->orWhereHas('gallery', function ($q) {
                      $q->where('duong_dan_anh', 'like', '%unsplash.com%');
                  });
            });
        }

        $places = $query->limit($limit)->get();

        if ($places->isEmpty()) {
            return response()->json([
                'status'  => true,
                'message' => 'Không có địa điểm cần cập nhật ảnh.',
                'updated' => 0,
            ]);
        }

        $updated = 0;
        $results = [];

        foreach ($places as $place) {
            $imageUrl = $this->serp->getGoogleImage($place->ten_dia_diem, $place->dia_chi ?? 'Đà Nẵng');

            if ($imageUrl) {
                \App\Models\HinhAnhDiaDiem::updateOrCreate(
                    ['id_dia_diem' => $place->id, 'is_main' => true],
                    ['duong_dan_anh' => $imageUrl, 'sort_order' => 1]
                );
                
                $updated++;
                $results[] = ['id' => $place->id, 'name' => $place->ten_dia_diem, 'image' => $imageUrl, 'ok' => true];
            } else {
                $results[] = ['id' => $place->id, 'name' => $place->ten_dia_diem, 'ok' => false];
            }

            usleep(500000);
        }

        return response()->json([
            'status'  => true,
            'message' => "Đã cập nhật ảnh cho {$updated}/{$places->count()} địa điểm.",
            'updated' => $updated,
            'results' => $results,
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->query('query');
        if (!$query) {
            return response()->json(['status' => false, 'message' => 'Thiếu từ khóa tìm kiếm.'], 400);
        }

        $results = $this->serp->crawlGoogleMaps($query);

        return response()->json(['status' => true, 'data' => $results]);
    }

    /**
     * Import địa điểm từ Google Maps vào DB.
     * Tự động crawl 5 ảnh gallery + 5 đánh giá sau khi tạo.
     */
    public function import(Request $request)
    {
        $data        = $request->all();
        $loai        = $request->input('loai_dia_diem');
        $id_danh_muc = $request->input('id_danh_muc');

        if (!$loai) {
            return response()->json(['status' => false, 'message' => 'Thiếu loại địa điểm.'], 400);
        }

        // Kiểm tra trùng theo tên
        $existing = DiaDiem::where('ten_dia_diem', $data['ten_dia_diem'])->first();
        if ($existing) {
            return response()->json(['status' => false, 'message' => 'Địa điểm này đã tồn tại trong hệ thống.'], 409);
        }

        // Thiết lập giá trị mặc định
        $data['danh_gia_trung_binh'] = $data['danh_gia_trung_binh'] ?? 0;
        if (!isset($data['gia_ve']))      $data['gia_ve']      = rand(0, 1) ? 0 : rand(20, 200) * 1000;
        if (!isset($data['gio_mo_cua']))  $data['gio_mo_cua']  = '0' . rand(6, 8) . ':00:00';
        if (!isset($data['gio_dong_cua'])) $data['gio_dong_cua'] = rand(18, 22) . ':00:00';

        $place = DiaDiem::create($data);

        // Liên kết danh mục
        if ($id_danh_muc) {
            \App\Models\ChiTietDanhMuc::create([
                'id_danh_muc' => $id_danh_muc,
                'id_dia_diem' => $place->id
            ]);
        }

        // ─── Crawl 10 ảnh gallery từ Google Images ───────────────────────────────
        try {
            $images = $this->serp->getMultipleImages(
                $place->ten_dia_diem,
                $place->dia_chi ?? 'Đà Nẵng',
                10
            );

            foreach ($images as $index => $imgUrl) {
                HinhAnhDiaDiem::create([
                    'id_dia_diem'   => $place->id,
                    'duong_dan_anh' => $imgUrl,
                    'is_main'       => $index === 0,
                    'sort_order'    => $index === 0 ? 0 : $index
                ]);
            }
        } catch (\Exception $e) {
            Log::warning("Import: Không crawl được ảnh cho '{$place->ten_dia_diem}': " . $e->getMessage());
        }

        // ─── Crawl 5 đánh giá từ Google Maps Reviews ────────────────────────────
        try {
            $reviews = $this->serp->getGoogleReviews(
                $place->ten_dia_diem,
                $place->dia_chi ?? 'Đà Nẵng'
            );

            foreach ($reviews as $rv) {
                DanhGia::create([
                    'id_nguoi_dung'         => null,
                    'id_dia_diem'           => $place->id,
                    'so_sao'               => $rv['so_sao'],
                    'noi_dung'             => $rv['noi_dung'],
                    'ten_nguoi_danh_gia'    => $rv['ten_nguoi_danh_gia'],
                    'avatar_nguoi_danh_gia' => $rv['avatar_nguoi_danh_gia'],
                    'la_danh_gia_google'   => true,
                ]);
            }
        } catch (\Exception $e) {
            Log::warning("Import: Không crawl được reviews cho '{$place->ten_dia_diem}': " . $e->getMessage());
        }

        // Load lại với gallery
        $place->load('gallery');

        return response()->json([
            'status'  => true,
            'message' => 'Import địa điểm thành công! Đã crawl ảnh và đánh giá.',
            'data'    => $place
        ]);
    }

    /**
     * Crawl đánh giá hàng loạt cho các địa điểm trong DB.
     * POST /api/serp/crawl-reviews
     * Body: { ids: [1,2,3], limit: 3 }
     */
    public function crawlReviews(Request $request)
    {
        // Tăng thời gian chạy vì mỗi địa điểm cần 2 lần gọi SerpApi
        set_time_limit(300);
        ini_set('max_execution_time', 300);

        $ids   = $request->input('ids', []);
        $limit = (int) $request->input('limit', 3); // Giảm default xuống 3

        $query = DiaDiem::query();

        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        } else {
            // Chỉ xử lý địa điểm chưa có đánh giá Google
            $query->whereDoesntHave('danhGias', function ($q) {
                $q->where('la_danh_gia_google', true);
            });
        }

        $places = $query->limit($limit)->get();

        if ($places->isEmpty()) {
            return response()->json([
                'status'  => true,
                'message' => 'Không có địa điểm cần crawl đánh giá.',
                'total'   => 0,
            ]);
        }

        $totalInserted = 0;
        $results       = [];

        foreach ($places as $place) {
            try {
                $reviews = $this->serp->getGoogleReviews(
                    $place->ten_dia_diem,
                    $place->dia_chi ?? 'Đà Nẵng'
                );

                $inserted = 0;
                foreach ($reviews as $rv) {
                    DanhGia::create([
                        'id_nguoi_dung'         => null,
                        'id_dia_diem'           => $place->id,
                        'so_sao'               => $rv['so_sao'],
                        'noi_dung'             => $rv['noi_dung'],
                        'ten_nguoi_danh_gia'    => $rv['ten_nguoi_danh_gia'],
                        'avatar_nguoi_danh_gia' => $rv['avatar_nguoi_danh_gia'],
                        'la_danh_gia_google'   => true,
                    ]);
                    $inserted++;
                }

                $totalInserted += $inserted;
                $results[] = [
                    'id'        => $place->id,
                    'name'      => $place->ten_dia_diem,
                    'inserted'  => $inserted,
                    'ok'        => $inserted > 0,
                ];

                sleep(1); // tránh rate limit
            } catch (\Exception $e) {
                Log::error("crawlReviews [{$place->ten_dia_diem}]: " . $e->getMessage());
                $results[] = [
                    'id'       => $place->id,
                    'name'     => $place->ten_dia_diem,
                    'inserted' => 0,
                    'ok'       => false,
                    'error'    => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'status'  => true,
            'message' => "Đã crawl {$totalInserted} đánh giá cho {$places->count()} địa điểm.",
            'total'   => $totalInserted,
            'results' => $results,
        ]);
    }

    /**
     * Crawl 5 ảnh gallery hàng loạt cho các địa điểm trong DB.
     * POST /api/serp/crawl-images
     * Body: { ids: [1,2,3], limit: 5 }
     */
    public function crawlImages(Request $request)
    {
        set_time_limit(300);
        ini_set('max_execution_time', 300);

        $ids   = $request->input('ids', []);
        $limit = (int) $request->input('limit', 5); // Giảm default xuống 5

        $query = DiaDiem::query();

        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        } else {
            $query->whereDoesntHave('gallery');
        }

        $places = $query->limit($limit)->get();

        if ($places->isEmpty()) {
            return response()->json([
                'status'  => true,
                'message' => 'Không có địa điểm cần crawl ảnh.',
                'total'   => 0,
            ]);
        }

        $totalInserted = 0;
        $results       = [];

        foreach ($places as $place) {
            try {
                $images = $this->serp->getMultipleImages(
                    $place->ten_dia_diem,
                    $place->dia_chi ?? 'Đà Nẵng',
                    10
                );

                $inserted = 0;
                foreach ($images as $index => $imgUrl) {
                    HinhAnhDiaDiem::create([
                        'id_dia_diem'   => $place->id,
                        'duong_dan_anh' => $imgUrl,
                        'is_main'       => $index === 0,
                        'sort_order'    => $index === 0 ? 0 : $index
                    ]);
                    $inserted++;
                }

                $totalInserted += $inserted;
                $results[] = [
                    'id'       => $place->id,
                    'name'     => $place->ten_dia_diem,
                    'inserted' => $inserted,
                    'ok'       => $inserted > 0,
                ];

                usleep(500000);
            } catch (\Exception $e) {
                Log::error("crawlImages [{$place->ten_dia_diem}]: " . $e->getMessage());
                $results[] = [
                    'id'       => $place->id,
                    'name'     => $place->ten_dia_diem,
                    'inserted' => 0,
                    'ok'       => false,
                    'error'    => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'status'  => true,
            'message' => "Đã crawl {$totalInserted} ảnh cho {$places->count()} địa điểm.",
            'total'   => $totalInserted,
            'results' => $results,
        ]);
    }
}
