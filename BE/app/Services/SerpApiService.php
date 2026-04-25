<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SerpApiService
{
    private string $apiKey;
    private string $baseUrl = 'https://serpapi.com/search.json';

    public function __construct()
    {
        $this->apiKey = config('services.serpapi.key', '68caa9575e20b9cff4b8584036033662965e7eafe884f807025788d5cb1ceea3');
    }

    /**
     * Tìm 1 ảnh thật từ Google Images cho một địa điểm
     */
    public function getGoogleImage(string $tenDiaDiem, string $diaChi = ''): ?string
    {
        $images = $this->getMultipleImages($tenDiaDiem, $diaChi, 1);
        return $images[0] ?? null;
    }

    /**
     * Lấy nhiều ảnh từ Google Images cho một địa điểm (tối đa $count ảnh)
     */
    public function getMultipleImages(string $tenDiaDiem, string $diaChi = '', int $count = 5): array
    {
        $query = trim($tenDiaDiem . ' ' . $diaChi . ' Đà Nẵng');

        try {
            $response = Http::timeout(12)->get($this->baseUrl, [
                'engine'  => 'google_images',
                'q'       => $query,
                'api_key' => $this->apiKey,
                'num'     => $count + 5,
                'hl'      => 'vi',
            ]);

            if (!$response->successful()) {
                Log::warning("SerpApi Images: Lỗi HTTP {$response->status()} cho '{$query}'");
                return [];
            }

            $data    = $response->json();
            $results = $data['images_results'] ?? [];
            $images  = [];

            // Ưu tiên ảnh gốc kích thước lớn
            foreach ($results as $img) {
                if (count($images) >= $count) break;
                $original = $img['original'] ?? null;
                $width    = $img['original_width'] ?? 0;
                $height   = $img['original_height'] ?? 0;

                if ($original && $width >= 400 && $height >= 300 && $this->isStableImageUrl($original)) {
                    $images[] = $original;
                }
            }

            // Fallback: dùng thumbnail nếu chưa đủ
            foreach ($results as $img) {
                if (count($images) >= $count) break;
                $thumb = $img['thumbnail'] ?? null;
                if ($thumb && !in_array($thumb, $images)) {
                    $images[] = $thumb;
                }
            }

            return $images;
        } catch (\Exception $e) {
            Log::error("SerpApi MultiImages Exception cho '{$query}': " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy tối đa 5 đánh giá thực từ Google Maps cho một địa điểm.
     * Dùng 2 bước: (1) tìm địa điểm lấy data_id, (2) lấy reviews qua data_id.
     */
    public function getGoogleReviews(string $tenDiaDiem, string $diaChi = ''): array
    {
        $query = trim($tenDiaDiem . ' ' . ($diaChi ?: 'Đà Nẵng'));

        try {
            // Bước 1: Tìm địa điểm → lấy data_id
            $searchResp = Http::timeout(12)->get($this->baseUrl, [
                'engine'  => 'google_maps',
                'q'       => $query,
                'api_key' => $this->apiKey,
                'hl'      => 'vi',
                'type'    => 'search',
            ]);

            if (!$searchResp->successful()) {
                Log::warning("SerpApi Reviews Step1: Lỗi HTTP {$searchResp->status()} cho '{$query}'");
                return [];
            }

            $searchData   = $searchResp->json();
            $localResults = $searchData['local_results'] ?? [];

            if (empty($localResults) && isset($searchData['place_results'])) {
                $localResults = [$searchData['place_results']];
            }

            if (empty($localResults)) {
                Log::info("SerpApi Reviews: Không tìm thấy địa điểm cho '{$query}'");
                return [];
            }

            $place  = $localResults[0];
            $dataId = $place['data_id'] ?? $place['place_id'] ?? null;

            if (!$dataId) {
                Log::info("SerpApi Reviews: Không có data_id cho '{$tenDiaDiem}'");
                return [];
            }

            // Bước 2: Lấy reviews từ data_id
            $reviewsResp = Http::timeout(15)->get($this->baseUrl, [
                'engine'  => 'google_maps_reviews',
                'data_id' => $dataId,
                'api_key' => $this->apiKey,
                'hl'      => 'vi',
                'sort_by' => 'newestFirst',
            ]);

            if (!$reviewsResp->successful()) {
                Log::warning("SerpApi Reviews Step2: Lỗi HTTP {$reviewsResp->status()}");
                return [];
            }

            $rawReviews = $reviewsResp->json()['reviews'] ?? [];
            $reviews    = [];

            foreach (array_slice($rawReviews, 0, 5) as $rv) {
                $noiDung = $rv['snippet'] ?? $rv['text'] ?? null;
                if (!$noiDung) continue; // bỏ qua review không có nội dung

                $reviews[] = [
                    'ten_nguoi_danh_gia'    => $rv['user']['name'] ?? 'Người dùng Google',
                    'avatar_nguoi_danh_gia' => $rv['user']['thumbnail'] ?? null,
                    'so_sao'               => (int) ($rv['rating'] ?? 4),
                    'noi_dung'             => $noiDung,
                    'la_danh_gia_google'   => true,
                ];
            }

            return $reviews;
        } catch (\Exception $e) {
            Log::error("SerpApi Reviews Exception cho '{$query}': " . $e->getMessage());
            return [];
        }
    }

    /**
     * Tìm thông tin chi tiết địa điểm từ Google Maps
     */
    public function getGoogleMapsPlace(string $tenDiaDiem, string $diaChi = ''): ?array
    {
        $query = trim($tenDiaDiem . ' ' . $diaChi);

        try {
            $response = Http::timeout(20)->get($this->baseUrl, [
                'engine'  => 'google_maps',
                'q'       => $query,
                'api_key' => $this->apiKey,
                'hl'      => 'vi',
                'type'    => 'search',
            ]);

            if (!$response->successful()) {
                Log::warning("SerpApi Maps: Lỗi HTTP {$response->status()} cho '{$query}'");
                return null;
            }

            $data    = $response->json();
            $results = $data['local_results'] ?? [];

            if (empty($results)) {
                return null;
            }

            $place = $results[0];

            return [
                'ten_dia_diem'        => $place['title'] ?? null,
                'dia_chi'             => $place['address'] ?? null,
                'danh_gia_trung_binh' => $place['rating'] ?? null,
                'vi_do'               => $place['gps_coordinates']['latitude'] ?? null,
                'kinh_do'             => $place['gps_coordinates']['longitude'] ?? null,
                'mo_ta'               => $place['description'] ?? null,
                'image'               => $place['thumbnail'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error("SerpApi Maps Exception cho '{$query}': " . $e->getMessage());
            return null;
        }
    }

    /**
     * Thu thập danh sách nhiều địa điểm từ Google Maps
     * Phục vụ cho tính năng crawl thẳng vào Database
     */
    public function crawlGoogleMaps(string $query): array
    {
        if (!str_contains(strtolower($query), 'đà nẵng') && !str_contains(strtolower($query), 'da nang')) {
            $query .= ' Đà Nẵng';
        }

        try {
            $response = Http::timeout(40)->get($this->baseUrl, [
                'engine'  => 'google_maps',
                'q'       => $query,
                'api_key' => $this->apiKey,
                'hl'      => 'vi',
                'type'    => 'search',
            ]);

            if (!$response->successful()) {
                Log::warning("SerpApi Crawl: Lỗi HTTP {$response->status()} cho '{$query}'");
                return [];
            }

            $data    = $response->json();
            $results = $data['local_results'] ?? [];

            if (empty($results) && isset($data['place_results'])) {
                $results = [$data['place_results']];
            }

            $places = [];
            foreach ($results as $place) {
                $lat = $place['gps_coordinates']['latitude'] ?? null;
                $lng = $place['gps_coordinates']['longitude'] ?? null;

                $places[] = [
                    'ten_dia_diem'        => $place['title'] ?? null,
                    'dia_chi'             => $place['address'] ?? null,
                    'danh_gia_trung_binh' => $place['rating'] ?? null,
                    'vi_do'               => $lat,
                    'kinh_do'             => $lng,
                    'mo_ta'               => $place['description'] ?? null,
                    'image'               => $place['thumbnail'] ?? null,
                ];
            }

            return $places;
        } catch (\Exception $e) {
            Log::error("SerpApi Crawl Exception cho '{$query}': " . $e->getMessage());
            return [];
        }
    }

    /**
     * Kiểm tra URL ảnh có từ nguồn ổn định không
     */
    private function isStableImageUrl(string $url): bool
    {
        $unstableDomains = ['encrypted-tbn', 'data:image', 'gstatic.com/images'];

        foreach ($unstableDomains as $domain) {
            if (str_contains($url, $domain)) {
                return false;
            }
        }

        return str_starts_with($url, 'http');
    }

    /**
     * Lấy số lượng searches còn lại trong tháng
     */
    public function getAccountInfo(): array
    {
        try {
            $response = Http::timeout(10)->get('https://serpapi.com/account', [
                'api_key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'searches_remaining' => $data['plan_searches_left'] ?? 0,
                    'plan'               => $data['plan_name'] ?? 'unknown',
                ];
            }
        } catch (\Exception $e) {
            Log::error('SerpApi account check failed: ' . $e->getMessage());
        }

        return ['searches_remaining' => 0, 'plan' => 'error'];
    }
}
