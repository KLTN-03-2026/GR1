<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DiaDiem;
use App\Services\RecommendationService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiController extends Controller
{
    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Tạo lịch trình tự động bằng mô hình lai (Hybrid).
     * 1. Thuật toán: Phân tích sở thích (Content-based) + Sắp xếp quy tắc (Rule-based).
     * 2. AI: Tinh chỉnh nội dung và lời khuyên (Refinement).
     */
    public function generateItinerary(Request $request)
    {
        set_time_limit(180); // Tăng thời gian tối đa để tránh lỗi Timeout khi gọi AI
        Log::info('AI Itinerary Generation started (Hybrid Mode)', ['payload' => $request->all()]);

        $ngayBatDau = $request->ngay_bat_dau;
        $ngayKetThuc = $request->ngay_ket_thuc;
        $budget = $request->ngan_sach_du_kien ?? 0;
        $notes = $request->chu_thich ?? '';

        // Tính số ngày
        $days = (strtotime($ngayKetThuc) - strtotime($ngayBatDau)) / 86400 + 1;
        $days = max(1, round($days));

        // Trích xuất từ khóa sở thích từ ghi chú
        $preferences = $this->extractPreferences($notes);

        $people = $request->so_luong_thanh_vien ?? 1;
        $weatherData = $request->input('weather_data', []); // Dữ liệu thời tiết từ frontend

        try {
            // --- BƯỚC 1: Thuật toán Content-based & Rule-based ---
            $technicalItinerary = $this->recommendationService->getRecommendedItinerary(
                $preferences, (int)$days, (float)$budget, (int)$people, $ngayBatDau, $weatherData
            );

            // --- BƯỚC 2: Tinh chỉnh bằng AI (Nếu có API Key) ---
            $apiKey = config('services.gemini.key');
            if ($apiKey) {
                return $this->refineWithAi($technicalItinerary, $notes, $apiKey);
            }

            // Nếu không có AI, trả về kết quả thuật toán thẳng (Technical Only)
            Log::info('Gemini API Key missing, returning technical-only itinerary');
            return response()->json([
                'status' => 'success',
                'data' => $technicalItinerary,
                'is_technical_only' => true
            ]);
        } catch (\Exception $e) {
            Log::critical('Error in Hybrid Itinerary Generation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Hệ thống đang bận, vui lòng thử lại sau.'
            ], 500);
        }
    }

    /**
     * Sử dụng AI để tinh chỉnh mô tả và thêm lời khuyên du lịch chuyên nghiệp.
     */
    private function refineWithAi($itinerary, $userNotes, $apiKey)
    {
        // 1. TỐI ƯU TOKENS: Lọc dữ liệu mỏng nhất để gửi cho AI
        $simplified = [];
        foreach ($itinerary as $day) {
            foreach ($day as $place) {
                $simplified[(string)$place['id_dia_diem']] = [
                    'id_dia_diem'   => $place['id_dia_diem'],
                    'ten_dia_diem'  => $place['ten_dia_diem'],
                    'gio'           => $place['gio'],
                    'loai_dia_diem' => $place['loai_dia_diem'] ?? '',
                ];
            }
        }

        $prompt = $this->buildRefinementPrompt(array_values($simplified), $userNotes);
        // gemini-1.5-pro không khả dụng với key này; dùng gemini-2.5-flash (confirmed 200 OK)
        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        $startTime = microtime(true);

        try {
            $response = Http::timeout(45)->post($apiUrl, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature'     => 0.2,
                    'maxOutputTokens' => 4000,
                    'responseMimeType' => 'application/json'
                ]
            ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text');
                $refinedData = $this->extractJsonObject($text);

                if ($refinedData && is_array($refinedData)) {
                    foreach ($itinerary as &$day) {
                        foreach ($day as &$place) {
                            $id = (string)$place['id_dia_diem'];
                            if (isset($refinedData[$id])) {
                                $r = $refinedData[$id];
                                $place['gio_bat_dau']     = $r['gio_bat_dau']     ?? $place['gio'];
                                $place['gio_ket_thuc']    = $r['gio_ket_thuc']    ?? null;
                                $place['thoi_luong_phut'] = $r['thoi_luong_phut'] ?? null;
                                $place['ghi_chu']         = $r['ghi_chu']         ?? $place['ghi_chu'];
                                $place['travel_tips']     = $r['travel_tips']     ?? '';
                            }
                        }
                    }

                    $executionTime = round(microtime(true) - $startTime, 2);
                    Log::info("AI Refinement complete in ultra-fast flat-object mode. Time taken: {$executionTime}s");
                    return response()->json([
                        'status' => 'success',
                        'data' => $itinerary,
                        'method' => 'hybrid-ai-refined',
                        'ai_execution_time_seconds' => $executionTime
                    ]);
                }
            }

            // Lấy log cụ thể từ Google để biết vì sao sập (Ví dụ Quota 429)
            $errorBody = $response ? $response->body() : 'No response';
            Log::warning("AI Refinement failed. Status: " . ($response->status() ?? 'N/A') . " Response: $errorBody");
        } catch (\Exception $e) {
            Log::error('Exception during AI refinement: ' . $e->getMessage());
        }

        // Fallback: Trả về lịch trình kỹ thuật nếu AI lỗi
        return response()->json([
            'status' => 'success',
            'data' => $itinerary,
            'is_technical_only' => true,
            'fallback_reason' => 'ai_service_unavailable'
        ]);
    }

    private function buildRefinementPrompt($simplifiedItinerary, $userNotes)
    {
        $itineraryJson = json_encode($simplifiedItinerary, JSON_UNESCAPED_UNICODE);

        return "
Bạn là AI Travel Optimizer cho lịch trình Đà Nẵng.

=========================
VAI TRÒ

- Không tạo địa điểm mới
- Không xóa địa điểm
- Giữ nguyên id_dia_diem
- Có thể đổi thứ tự trong cùng ngày để hợp lý hơn
- Không chuyển địa điểm sang ngày khác

=========================
ƯU TIÊN TỐI ƯU

1. TUYẾN ĐƯỜNG HỢP LÝ
   - Gom cụm địa điểm gần nhau trong cùng buổi
   - Tránh di chuyển zigzag qua nhiều quận
   - Sáng → trung tâm / gần khách sạn
   - Chiều → dần ra ngoại ô hoặc bãi biển
   - Tối → khu vui chơi / cầu / chợ đêm

2. ĐA DẠNG TRẢI NGHIỆM
   - Không xếp 2 địa điểm cùng loại liên tiếp (ví dụ: không 2 quán ăn liền nhau)
   - Xen kẽ: Ăn → Tham quan → Cafe → Tham quan → Ăn tối → Giải trí
   - Mỗi ngày có ít nhất 1 trải nghiệm đặc trưng Đà Nẵng

3. BAN ĐÊM PHONG PHÚ (sau 19:00)
   - Ưu tiên: Cầu Rồng / cầu Tình Yêu / cầu Trần Thị Lý
   - Xen kẽ: cafe acoustic / quán nhậu hải sản / bar / chợ đêm
   - Không để tối chỉ toàn ăn uống đơn điệu

=========================
QUY TẮC THỜI GIAN THỰC TẾ

ĂN UỐNG:
- Ăn sáng: 30-45 phút
- Ăn trưa: 45-60 phút
- Ăn tối: 60-90 phút
- Ăn vặt / chè / kem: 20-30 phút

CAFE / CHECK-IN:
- Cafe thông thường: 30-60 phút
- Cafe view đẹp / sống ảo: 45-75 phút

THAM QUAN:
- Biển: 60-90 phút
- Cầu / điểm nhỏ: 30-45 phút
- Chùa / tâm linh: 45-75 phút
- Bảo tàng: 60-90 phút
- Chợ đêm / phố đi bộ: 60-90 phút
- Khu lớn (Bà Nà / Sơn Trà / Ngũ Hành Sơn): 180-240 phút

BAR / ACOUSTIC / PUB:
- 60-90 phút

=========================
QUY TẮC TÍNH GIỜ

- gio_ket_thuc = gio_bat_dau + thoi_luong_phut
- Không để slot chồng lên nhau
- Nếu đổi thứ tự → tính lại giờ cho toàn ngày bắt đầu từ slot đầu tiên
- Slot đầu tiên mỗi ngày giữ giờ ban đầu backend cung cấp

=========================
ƯU TIÊN NGƯỜI DÙNG

$userNotes

=========================
DỮ LIỆU LỊCH TRÌNH

$itineraryJson

=========================
FORMAT JSON BẮT BUỘC

{
  \"1\": {
    \"gio_bat_dau\": \"06:30\",
    \"gio_ket_thuc\": \"07:10\",
    \"thoi_luong_phut\": 40,
    \"ghi_chu\": \"Ăn sáng đặc sản địa phương, nên đi sớm\",
    \"travel_tips\": \"Gọi thêm chả cá chiên, đừng quên nước mắm ớt\"
  }
}

QUY TẮC OUTPUT:
- Trả đầy đủ TẤT CẢ id_dia_diem có trong input.
- Chỉ trả JSON object thuần túy.
- Không markdown, không giải thích, không comment.
- ghi_chu: mô tả ngắn gọn lý do / đặc điểm địa điểm này trong ngày.
- travel_tips: lời khuyên thực tế như local expert (ví dụ: đặt bàn trước, tránh giờ cao điểm, mang gì theo).
";
    }

    private function extractPreferences($notes)
    {
        $keywords = ['biển', 'ẩm thực', 'ăn uống', 'check-in', 'chụp ảnh', 'văn hóa', 'tâm linh', 'giải trí', 'trẻ em', 'gia đình', 'lãng mạn'];
        $found = [];
        $notesLow = mb_strtolower($notes);

        foreach ($keywords as $kw) {
            if (mb_strpos($notesLow, $kw) !== false) {
                $found[] = $kw;
            }
        }

        return $found;
    }

    private function extractJsonObject($text)
    {
        // Loại bỏ markdown code block nếu có
        $text = preg_replace('/```(?:json)?|```/', '', $text);
        $text = trim($text);

        $startPos = strpos($text, '{');
        $endPos = strrpos($text, '}');

        if ($startPos !== false && $endPos !== false) {
            $jsonText = substr($text, $startPos, $endPos - $startPos + 1);
            return json_decode($jsonText, true);
        }
        return null;
    }
}
