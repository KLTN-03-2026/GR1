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
    /**
     * Tối ưu lại một lịch trình đã tồn tại.
     * AI sẽ review thứ tự và thêm tips mà không thêm/xóa địa điểm.
     */
    public function reorderWithAi(Request $request, $id)
    {
        set_time_limit(180);
        $chuyenDi = \App\Models\ChuyenDi::find($id);
        if (!$chuyenDi) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy chuyến đi.'], 404);
        }

        $apiKey = config('services.gemini.key');
        if (!$apiKey) {
            return response()->json(['status' => 'error', 'message' => 'Hệ thống AI chưa được cấu hình.'], 500);
        }

        // Lấy danh sách địa điểm hiện tại
        $lichTrinhs = \App\Models\LichTrinhDiaDiem::where('id_chuyen_di', $id)
            ->orderBy('thu_tu_tham_quan')
            ->get();

        if ($lichTrinhs->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Lịch trình trống.'], 400);
        }

        // Build itinerary array in format expected by refineWithAi
        $itineraryByDay = [];
        foreach ($lichTrinhs as $lt) {
            $dayIdx = floor(($lt->thu_tu_tham_quan - 1) / 100);
            $diaDiem = \App\Models\DiaDiem::find($lt->id_dia_diem);
            
            $itineraryByDay[$dayIdx][] = [
                'id' => $lt->id, // Chúng ta cần ID này để update ngược lại sau này
                'id_dia_diem' => $lt->id_dia_diem ?? 'free_time_' . $lt->id,
                'ten_dia_diem' => $diaDiem ? $diaDiem->ten_dia_diem : 'Thời gian tự do',
                'gio' => $lt->gio_bat_dau,
                'loai_dia_diem' => $diaDiem ? $diaDiem->loai_dia_diem : 'Nghỉ ngơi',
                'thoi_luong_phut' => $lt->thoi_luong_phut
            ];
        }

        $res = $this->refineWithAi($itineraryByDay, $chuyenDi->chu_thich ?? '', $apiKey);
        $json = $res->getData(true);

        if ($json['status'] === 'success' && !isset($json['is_technical_only'])) {
            // Cập nhật lại vào DB
            foreach ($json['data'] as $dayIdx => $dayItems) {
                foreach ($dayItems as $pos => $item) {
                    $lt = \App\Models\LichTrinhDiaDiem::find($item['id']);
                    if ($lt) {
                        $lt->thu_tu_tham_quan = $dayIdx * 100 + $pos + 1;
                        $lt->gio_bat_dau = $item['gio_bat_dau'];
                        $lt->gio_ket_thuc = $item['gio_ket_thuc'];
                        $lt->thoi_luong_phut = $item['thoi_luong_phut'];
                        
                        // Lưu tips vào ghi chú (theo format của frontend)
                        $cleanNote = explode('|AI_TIPS|', $lt->ghi_chu ?? '')[0];
                        if (isset($item['travel_tips']) && !empty($item['travel_tips'])) {
                            $lt->ghi_chu = $cleanNote . '|AI_TIPS|' . $item['travel_tips'];
                        }
                        
                        $lt->save();
                    }
                }
            }

            // Broadcast realtime update
            if ($chuyenDi->id_nhom_du_lich) {
                broadcast(new \App\Events\ItineraryReordered(
                    $chuyenDi->id_nhom_du_lich,
                    $id,
                    ['message' => 'AI reorder completed']
                ))->toOthers();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Lịch trình đã được AI tối ưu lại.',
                'data' => $json['data']
            ]);
        }

        return $res;
    }

    /**
     * Tái tối ưu lịch trình dựa trên chi phí thực tế (Tầng 2 + Tầng 3).
     * API trả về preview lịch trình mới — KHÔNG lưu DB ngay.
     * Người dùng xác nhận ở bước tiếp theo.
     */
    public function reOptimizeWithBudget(Request $request, $id)
    {
        set_time_limit(180);

        $chuyenDi = \App\Models\ChuyenDi::find($id);
        if (!$chuyenDi) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy chuyến đi.'], 404);
        }

        // ── Tầng 2: Tính ngân sách thực còn lại ───────────────────────────
        $nganSachGoc = (float)$chuyenDi->ngan_sach;
        $soNgay      = (int)$chuyenDi->so_ngay;
        $soNguoi     = (int)($chuyenDi->so_nguoi ?? 1);
        $startDate   = $chuyenDi->ngay_bat_dau;

        // Lấy lịch trình đã có
        $allSlots = \App\Models\LichTrinhDiaDiem::where('id_chuyen_di', $id)->get();

        // Ngày đã đi (trước hôm nay hoặc user chỉ định)
        $reOptimizeFromDay = max(0, (int)$request->input('from_day', 1)); // 1-indexed
        $today = date('Y-m-d');

        // Tính chi phí ước tính các ngày đã đi (từ gia_ve địa điểm × soNguoi)
        $estimatedSpentDaysBefore = 0;
        $usedIds = [];
        $daysRemaining = 0;
        $newStartDate = $startDate;

        foreach ($allSlots as $slot) {
            // ✅ FIX: Dùng thu_tu_tham_quan để xác định ngày (không dùng thoi_gian vì thường là null)
            // Slot ngày N có thu_tu_tham_quan = (N-1)*100 + pos + 1
            // → dayNumber (1-indexed) = floor((thu_tu - 1) / 100) + 1
            $thuTu = (int)($slot->thu_tu_tham_quan ?? 0);
            $slotDayNumber = $thuTu > 0 ? (int)(floor(($thuTu - 1) / 100) + 1) : 1;

            $isBeforeReOptimizeDay = $slotDayNumber < $reOptimizeFromDay;

            if ($isBeforeReOptimizeDay) {
                // Ngày đã đi: cộng chi phí ước tính + lưu usedIds
                $diaDiem = $slot->id_dia_diem ? \App\Models\DiaDiem::find($slot->id_dia_diem) : null;
                if ($diaDiem) {
                    $estimatedSpentDaysBefore += (float)$diaDiem->gia_ve * $soNguoi;
                    $usedIds[] = $slot->id_dia_diem;
                }
            }
        }

        // Ngày bắt đầu tái tối ưu
        $reOptimizeStartDate = date('Y-m-d', strtotime($startDate . ' +' . ($reOptimizeFromDay - 1) . ' days'));
        $daysRemaining = $soNgay - $reOptimizeFromDay + 1;

        if ($daysRemaining <= 0) {
            return response()->json(['status' => 'error', 'message' => 'Không còn ngày nào để tái tối ưu.'], 400);
        }

        // Lấy chi phí phát sinh thực tế
        $chiPhis = \App\Models\ChiPhiPhatSinh::where('id_chuyen_di', $id)->get()->toArray();

        // ── Tầng 3: Phân tích spending profile ────────────────────────────
        $spendingProfile = \App\Services\RecommendationService::analyzeSpendingProfile(
            $chiPhis,
            $nganSachGoc,
            $estimatedSpentDaysBefore
        );

        // Tổng chi phí phát sinh
        $totalIncurred = $spendingProfile['total_incurred'];

        // Ngân sách thực còn lại = Gốc - Đã dùng (ước tính) - Phát sinh
        // Giữ nguyên số âm để getRecommendedItineraryReOptimize biết đang vượt ngân sách thực
        $remainingBudgetRaw  = $nganSachGoc - $estimatedSpentDaysBefore - $totalIncurred;
        $remainingBudget     = $remainingBudgetRaw; // Có thể âm

        // ── Override spending profile khi ngân sách cạn hoặc âm ──────────────
        if ($remainingBudget <= 0) {
            $spendingProfile['budget_mode']    = true;
            $spendingProfile['force_free_only'] = true;
            $spendingProfile['is_over_budget']  = true;
            Log::info('[reOptimizeWithBudget] Ngân sách cạn hoặc âm → bật FREE-ONLY', [
                'remaining_raw' => $remainingBudgetRaw
            ]);
        } elseif ($remainingBudget < ($nganSachGoc * 0.10)) {
            // Dưới 10% ngân sách gốc → bật budget_mode
            $spendingProfile['budget_mode']   = true;
            $spendingProfile['is_over_budget'] = true;
        }

        Log::info('[reOptimizeWithBudget]', [
            'id'                      => $id,
            'ngan_sach_goc'           => $nganSachGoc,
            'estimated_spent'         => $estimatedSpentDaysBefore,
            'total_incurred'          => $totalIncurred,
            'remaining_budget'        => $remainingBudget,
            'days_remaining'          => $daysRemaining,
            're_optimize_start'       => $reOptimizeStartDate,
            'spending_profile'        => $spendingProfile,
        ]);

        // Lấy sở thích từ ghi chú chuyến đi
        $notes = $chuyenDi->chu_thich ?? '';
        $preferences = $this->extractPreferences($notes);

        // Gọi thuật toán tái tối ưu
        $newItinerary = $this->recommendationService->getRecommendedItineraryReOptimize(
            $preferences,
            $daysRemaining,
            $remainingBudget,
            $soNguoi,
            $reOptimizeStartDate,
            array_unique($usedIds),
            $spendingProfile,
            $request->input('weather_data', [])
        );

        return response()->json([
            'status'                  => 'success',
            'message'                 => 'Tái tối ưu thành công. Vui lòng xác nhận để áp dụng.',
            'data'                    => $newItinerary,
            'spending_profile'        => $spendingProfile,
            'remaining_budget'        => $remainingBudget,
            'days_remaining'          => $daysRemaining,
            'from_day'                => $reOptimizeFromDay,
            'start_date'              => $reOptimizeStartDate,
            'estimated_spent_before'  => $estimatedSpentDaysBefore,
            'total_incurred'          => $totalIncurred,
            'days_kept'               => $reOptimizeFromDay - 1, // Số ngày giữ nguyên
        ]);
    }

    /**
     * Xác nhận áp dụng lịch trình mới từ re-optimize vào database.
     */
    public function confirmReOptimize(Request $request, $id)
    {
        $chuyenDi = \App\Models\ChuyenDi::find($id);
        if (!$chuyenDi) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy chuyến đi.'], 404);
        }

        $fromDay     = (int)$request->input('from_day', 1);
        $newItinerary = $request->input('itinerary', []);

        if (empty($newItinerary)) {
            return response()->json(['status' => 'error', 'message' => 'Lịch trình mới trống.'], 400);
        }

        $startDate = $chuyenDi->ngay_bat_dau;

        // ✅ FIX: Xóa bằng thu_tu_tham_quan (luôn có giá trị), KHÔNG dùng thoi_gian (thường là null)
        // Slot thuộc ngày N có thu_tu_tham_quan = (N-1)*100 + vị_trí_trong_ngày + 1
        // → Ngày from_day trở đi có thu_tu_tham_quan >= (fromDay - 1) * 100 + 1
        $minThuTu = ($fromDay - 1) * 100 + 1;
        $deleted = \App\Models\LichTrinhDiaDiem::where('id_chuyen_di', $id)
            ->where('thu_tu_tham_quan', '>=', $minThuTu)
            ->delete();

        Log::info('[confirmReOptimize] Đã xóa slot cũ', ['deleted_count' => $deleted, 'min_thu_tu' => $minThuTu]);

        // Chèn lịch trình mới
        $dayOffset = $fromDay - 1;
        foreach ($newItinerary as $dayIdx => $daySlots) {
            $actualDate = date('Y-m-d', strtotime($startDate . ' +' . ($dayOffset + $dayIdx) . ' days'));
            foreach ($daySlots as $order => $slot) {
                $idDiaDiem = is_numeric($slot['id_dia_diem'] ?? null) ? (int)$slot['id_dia_diem'] : null;
                \App\Models\LichTrinhDiaDiem::create([
                    'id_chuyen_di'     => $id,
                    'id_dia_diem'      => $idDiaDiem,
                    'thu_tu_tham_quan' => ($dayOffset + $dayIdx) * 100 + $order + 1,
                    'thoi_gian'        => $actualDate,
                    'gio_bat_dau'      => $slot['gio_bat_dau'] ?? $slot['gio'] ?? null,
                    'gio_ket_thuc'     => $slot['gio_ket_thuc'] ?? null,
                    'thoi_luong_phut'  => $slot['thoi_luong_phut'] ?? null,
                    'chi_phi_du_kien'  => ($slot['gia_ve'] ?? 0) * ($chuyenDi->so_nguoi ?? 1),
                    'ghi_chu'          => $slot['ghi_chu'] ?? null,
                ]);
            }
        }

        Log::info('[confirmReOptimize] Đã áp dụng lịch trình tái tối ưu', [
            'id_chuyen_di' => $id,
            'from_day'     => $fromDay,
            'days_added'   => count($newItinerary),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Đã áp dụng lịch trình tái tối ưu thành công!',
        ]);
    }

    public function generatePlaceTips(Request $request)
    {
        $request->validate([
            'ten_dia_diem'  => 'required|string',
            'loai_dia_diem' => 'nullable|string',
            'dia_chi'       => 'nullable|string',
            'gio_bat_dau'   => 'nullable|string',
            'thoi_tiet'     => 'nullable|string'
        ]);

        $apiKey = config('services.gemini.key');
        if (!$apiKey) {
            return response()->json([
                'ghi_chu' => "Địa điểm: {$request->ten_dia_diem} — {$request->dia_chi}",
                'travel_tips' => "Loại hình: {$request->loai_dia_diem}. Vui lòng tham khảo thêm thông tin tại địa điểm."
            ]);
        }

        $prompt = <<<PROMPT
Bạn là chuyên gia du lịch Đà Nẵng. Khách hàng vừa đổi sang một địa điểm mới trong lịch trình.
Hãy tạo Ghi chú và Gợi ý cho địa điểm này.

Thông tin địa điểm:
- Tên: {$request->ten_dia_diem}
- Phân loại: {$request->loai_dia_diem}
- Địa chỉ: {$request->dia_chi}
- Giờ dự kiến đến: {$request->gio_bat_dau}
- Thời tiết dự kiến: {$request->thoi_tiet}

Format JSON bắt buộc trả về (chỉ trả JSON, không bọc markdown):
{
    "ghi_chu": "Lý do ngắn gọn 1 câu vì sao nên đi điểm này vào giờ/thời tiết này",
    "travel_tips": "Mẹo thực tế cụ thể 1-2 câu (gọi món gì, góc chụp nào, lưu ý gì)"
}
PROMPT;

        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";
        try {
            $response = Http::timeout(15)->post($apiUrl, [
                'contents'         => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => [
                    'temperature'    => 0.2,
                    'maxOutputTokens'=> 300,
                    'responseMimeType' => 'application/json',
                    'thinkingConfig' => ['thinkingBudget' => 0],
                ],
            ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text');
                $data = $this->extractJsonObject($text);
                if ($data && isset($data['ghi_chu']) && isset($data['travel_tips'])) {
                    return response()->json($data);
                }
            }
        } catch (\Exception $e) {
            Log::error('[generatePlaceTips] Lỗi: ' . $e->getMessage());
        }

        // Fallback
        return response()->json([
            'ghi_chu' => "Địa điểm: {$request->ten_dia_diem} — {$request->dia_chi}",
            'travel_tips' => "Loại hình: {$request->loai_dia_diem}. Vui lòng tham khảo thêm thông tin tại địa điểm."
        ]);
    }

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

        $people      = $request->so_luong_thanh_vien ?? 1;
        $weatherData = $request->input('weather_data', []);
        $apiKey      = config('services.gemini.key');

        // ── [PLAN D] Phân tích ghi chú bằng AI NLP ─────────────────────────
        $enrichedContext = [];
        if ($apiKey && !empty(trim($notes))) {
            $enrichedContext = $this->extractPreferencesWithAi($notes, $apiKey);
            $preferences     = $enrichedContext['preferences'] ?? [];
        } else {
            $preferences = $this->extractPreferences($notes);
        }

        Log::info('[Plan D] Preferences extracted', [
            'method'      => !empty($enrichedContext) ? 'AI-NLP' : 'keyword-match',
            'preferences' => $preferences,
            'group_type'  => $enrichedContext['group_type'] ?? 'N/A',
        ]);

        try {
            // --- BƯỚC 1: Thuật toán Content-based & Rule-based ---
            $technicalItinerary = $this->recommendationService->getRecommendedItinerary(
                $preferences, (int)$days, (float)$budget, (int)$people, $ngayBatDau, $weatherData
            );

            // --- BƯỚC 2: Tinh chỉnh bằng AI (Nếu có API Key) ---
            if ($apiKey) {
                return $this->refineWithAi($technicalItinerary, $notes, $apiKey, $enrichedContext);
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
    private function refineWithAi($itinerary, $userNotes, $apiKey, array $enrichedContext = [])
    {
        // [Plan B] Compact data - chỉ gửi fields cần thiết, bỏ null để tiết kiệm tokens
        $simplified = [];
        foreach ($itinerary as $dayIdx => $day) {
            foreach ($day as $place) {
                $item = [
                    'id'   => $place['id_dia_diem'],
                    'ten'  => $place['ten_dia_diem'],
                    'loai' => $place['loai_dia_diem'] ?? '',
                    'gio'  => $place['gio_bat_dau']   ?? $place['gio'],
                    'ngay' => $place['ngay_index']     ?? $dayIdx,
                    'wkey' => $place['window_key']     ?? null,
                    'tt'   => $place['thoi_tiet']      ?? null,
                ];
                // Chỉ thêm tọa độ nếu có (tiết kiệm tokens)
                if (!empty($place['vi_do']) && !empty($place['kinh_do'])) {
                    $item['lat'] = round((float)$place['vi_do'], 4);
                    $item['lng'] = round((float)$place['kinh_do'], 4);
                }
                // Chỉ thêm giờ mở cửa nếu có
                if (!empty($place['gio_mo_cua'])) {
                    $item['mo']   = $place['gio_mo_cua'];
                    $item['dong'] = $place['gio_dong_cua'] ?? null;
                }
                $simplified[(string)$place['id_dia_diem']] = $item;
            }
        }

        $prompt = $this->buildRefinementPrompt(array_values($simplified), $userNotes, $enrichedContext);
        // gemini-2.5-flash
        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        $startTime = microtime(true);

        try {
            // Retry tối đa 3 lần với exponential backoff (503/429 là lỗi tạm thời)
            $response    = null;
            $maxAttempts = 3;
            for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
                $response = Http::timeout(60)->post($apiUrl, [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'temperature'      => 0.2,
                        'maxOutputTokens'  => 8192,
                        'responseMimeType' => 'application/json',
                        'thinkingConfig'   => ['thinkingBudget' => 0],
                    ]
                ]);

                $statusCode = $response->status();
                if ($response->successful()) break; // Thành công → thoát vòng lặp

                // Chỉ retry với lỗi tạm thời
                if (!in_array($statusCode, [429, 503]) || $attempt === $maxAttempts) break;

                $delay = pow(2, $attempt); // 2s, 4s, 8s
                Log::warning("[Plan B] Gemini lỗi {$statusCode}, retry {$attempt}/{$maxAttempts} sau {$delay}s...");
                sleep($delay);
            }

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text');

                // Kiểm tra bị cắt giữa chừng
                $finishReason = $response->json('candidates.0.finishReason');
                if ($finishReason === 'MAX_TOKENS') {
                    Log::warning('[Plan B] Gemini response bị cắt (MAX_TOKENS). Prompt tokens: '
                        . $response->json('usageMetadata.promptTokenCount')
                        . ', thoughtsTokens: '
                        . $response->json('usageMetadata.thoughtsTokenCount'));
                    // Vẫn thử parse phần đã có
                }

                Log::info('[Plan B] Gemini OK — thoughtsTokens: '
                    . $response->json('usageMetadata.thoughtsTokenCount')
                    . ', outputTokens: '
                    . $response->json('usageMetadata.candidatesTokenCount')
                    . ', finishReason: ' . $finishReason);

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

    private function buildRefinementPrompt(array $simplifiedItinerary, string $userNotes, array $enrichedContext = []): string
    {
        $itineraryJson = json_encode($simplifiedItinerary, JSON_UNESCAPED_UNICODE);

        // ── [Plan B] Xây dựng section hồ sơ người dùng từ Plan D ──────────
        $profileSection = '';
        if (!empty($enrichedContext)) {
            $groupType    = $enrichedContext['group_type']          ?? 'general';
            $mood         = $enrichedContext['mood']                ?? '';
            $prefs        = implode(', ', $enrichedContext['preferences']       ?? []);
            $restrictions = implode(', ', $enrichedContext['food_restrictions'] ?? []);
            $specials     = implode(', ', $enrichedContext['special_requests']  ?? []);

            $profileSection = "=== HỒ SƠ NGƯỜI DÙNG (AI đã phân tích) ===\n"
                . "Loại nhóm    : {$groupType}\n"
                . "Phong cách   : {$mood}\n"
                . "Sở thích     : {$prefs}\n"
                . (!empty($restrictions) ? "Kiêng cữ ăn : {$restrictions}\n" : '')
                . (!empty($specials)     ? "Yêu cầu đặc biệt: {$specials}\n" : '');
        }

        $cleanNotes = !empty(trim($userNotes))
            ? "Ghi chú gốc của khách: \"{$userNotes}\""
            : '';

        return <<<PROMPT
Bạn là AI Travel Optimizer chuyên nghiệp cho lịch trình du lịch Đà Nẵng.

=== VAI TRÒ ===
- KHÔNG tạo địa điểm mới, KHÔNG xóa địa điểm
- Giữ nguyên id_dia_diem
- Có thể đổi thứ tự địa điểm trong cùng một ngày (cùng ngay_index)
- Không chuyển địa điểm sang ngày khác

{$profileSection}
=== GHI CHÚ NGƯỜI DÙNG ===
{$cleanNotes}
Nếu ghi chú vô nghĩa/rác/không liên quan du lịch → BỎ QUA, lên lịch chuẩn bình thường.

=== TỐI ƯU TUYẾN ĐƯỜNG (dùng lat, lng) ===
- Gom cụm địa điểm gần nhau trong cùng buổi (wkey giống nhau, ngay giống nhau)
- Tránh zigzag giữa các quận — ưu tiên di chuyển liền mạch
- Sáng: trung tâm / gần khách sạn → Chiều: ra ngoại ô / biển → Tối: khu vui chơi / cầu
- Ước lượng 10-15 phút di chuyển giữa 2 điểm nếu tọa độ xa nhau

=== TỐI ƯU THỜI TIẾT (dùng tt) ===
- "rain" hoặc "cloudy" → ưu tiên địa điểm trong nhà (cafe, bảo tàng, nhà hàng)
  Tránh bãi biển, công viên ngoài trời trong khung giờ đó
- "sunny" → khuyến khích outdoor, biển, check-in ngoài trời

=== KIỂM TRA GIỜ MỞ CỬA (dùng mo, dong) ===
- Nếu gio_bat_dau nằm ngoài [mo, dong] → điều chỉnh hoặc ghi chú cảnh báo
- Nếu mo=null → bỏ qua ràng buộc này

=== GIẢI THÍCH FIELD DỮ LIỆU ===
- id: id địa điểm (dùng làm key trong output)
- ten: tên địa điểm | loai: loại hình
- gio: giờ bắt đầu | ngay: chỉ số ngày (0=ngày 1, 1=ngày 2...)
- wkey: khung giờ (breakfast/morning/lunch/rest/afternoon/dinner/night)
- tt: thời tiết (sunny/rain/cloudy/partly_cloudy)
- lat/lng: tọa độ GPS | mo/dong: giờ mở/đóng cửa

=== NGÀY ĐẶC BIỆT ===
- "Cầu Rồng – Phun lửa" chỉ xếp 21:00, chỉ Thứ 6 và Thứ 7
- Mỗi ngày cần ít nhất 1 trải nghiệm đặc trưng Đà Nẵng

=== ĐA DẠNG TRẢI NGHIỆM ===
- Không xếp 2 địa điểm cùng loai_dia_diem liên tiếp
- Xen kẽ: Ăn → Tham quan → Cafe → Tham quan → Ăn tối → Giải trí
- Tối (window_key=night): Cầu / Chợ đêm / Cafe acoustic / Bar nhẹ

=== THỜI LƯỢNG CHUẨN ===
Ăn sáng: 30-45p | Ăn trưa: 45-60p | Ăn tối: 60-90p | Ăn vặt/Cafe: 20-40p
Biển: 60-90p | Chùa/Tâm linh: 45-75p | Bảo tàng: 60-90p
Chợ đêm/Phố đi bộ: 60-90p | Khu lớn (Bà Nà, NGS): 180-240p | Cầu/Điểm nhỏ: 30-45p
Bar/Acoustic/Pub: 60-90p

=== QUY TẮC TÍNH GIỜ ===
- gio_ket_thuc = gio_bat_dau + thoi_luong_phut
- Không để các slot trong cùng ngày chồng giờ
- Slot đầu tiên mỗi ngày giữ gio_bat_dau gốc
- Nếu đổi thứ tự → tính lại toàn bộ giờ trong ngày đó

=== DỮ LIỆU LỊCH TRÌNH ===
{$itineraryJson}

=== FORMAT JSON OUTPUT BẮT BUỘC ===
{
  "<id_dia_diem>": {
    "gio_bat_dau": "HH:MM",
    "gio_ket_thuc": "HH:MM",
    "thoi_luong_phut": <số nguyên>,
    "ghi_chu": "...",
    "travel_tips": "..."
  }
}

Quy tắc output:
- Trả ĐỦ TẤT CẢ id_dia_diem trong input
- Chỉ JSON object thuần túy — KHÔNG markdown, KHÔNG comment

PHÂN BIỆT ghi_chu và travel_tips (QUAN TRỌNG — không được viết trùng nội dung):

ghi_chu = Lý do địa điểm này PHÙ HỢP với khung giờ này (ngắn, 1 câu):
  Ví dụ ĐÚNG: "Ăn sáng lý tưởng trước khi tham quan khu trung tâm"
  Ví dụ ĐÚNG: "Nghỉ ngơi bên bờ biển sau buổi sáng dày đặc hoạt động"
  Ví dụ SAI: "Quán Mì Quảng nổi tiếng, nên đến sớm tránh đông" ← đây là tips, không phải ghi chú

travel_tips = Mẹo THỰC TẾ cụ thể về địa điểm đó mà local mới biết (1-2 câu):
  Ví dụ ĐÚNG: "Gọi mì gà trộn đặc biệt — không có trên menu. Đến trước 7h để có bàn ngồi ngoài trời."
  Ví dụ ĐÚNG: "Thuê xe máy 80k/ngày ngay cổng vào để tự khám phá. Tránh khu lưu niệm gần lối ra."
  Ví dụ SAI: "Đây là điểm tham quan tâm linh nổi tiếng của Đà Nẵng" ← quá chung chung

- Nếu có kiêng cữ ăn từ hồ sơ người dùng → travel_tips PHẢI cảnh báo nếu địa điểm đó liên quan
PROMPT;
    }

    /**
     * Fallback keyword match (dùng khi không có API key hoặc Plan D thất bại)
     */
    private function extractPreferences($notes): array
    {
        $keywords = ['biển', 'ẩm thực', 'ăn uống', 'check-in', 'chụp ảnh', 'văn hóa',
                     'tâm linh', 'giải trí', 'trẻ em', 'gia đình', 'lãng mạn', 'nhậu',
                     'acoustic', 'hải sản', 'cà phê', 'cafe', 'chùa', 'bảo tàng'];
        $found    = [];
        $notesLow = mb_strtolower($notes);
        foreach ($keywords as $kw) {
            if (mb_strpos($notesLow, $kw) !== false) {
                $found[] = $kw;
            }
        }
        return $found;
    }

    /**
     * [Plan D] Dùng Gemini Flash để phân tích free-text ghi chú của người dùng.
     * Chi phí: ~$0.00003/lần — cực rẻ, timeout ngắn (12s).
     * Fallback về extractPreferences() nếu AI không phản hồi.
     */
    private function extractPreferencesWithAi(string $notes, string $apiKey): array
    {
        $default = [
            'preferences'       => $this->extractPreferences($notes),
            'food_restrictions' => [],
            'special_requests'  => [],
            'group_type'        => 'general',
            'mood'              => '',
        ];

        $prompt = <<<PROMPT
Phân tích ghi chú du lịch Đà Nẵng sau và trả về JSON thuần túy (không markdown):
Ghi chú: "{$notes}"

Format bắt buộc:
{
  "preferences": ["ẩm thực đường phố", "biển"],
  "food_restrictions": ["không hải sản"],
  "special_requests": ["cần nghỉ trưa"],
  "group_type": "bạn bè",
  "mood": "vui vẻ, náo nhiệt"
}
- preferences: tối đa 8 sở thích du lịch cụ thể
- food_restrictions: dị ứng, kiêng ăn (mảng rỗng nếu không có)
- special_requests: yêu cầu về lịch trình (mảng rỗng nếu không có)
- group_type: solo / cặp đôi / gia đình / bạn bè / công ty / general
- mood: phong cách chuyến đi (1 câu ngắn)
Nếu ghi chú vô nghĩa → trả tất cả rỗng.
PROMPT;

        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        try {
            $response = Http::timeout(12)->post($apiUrl, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature'      => 0.1,
                    'responseMimeType' => 'application/json',
                    'thinkingConfig'   => ['thinkingBudget' => 0],
                ]
            ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text');
                // Dùng extractJsonObject để linh hoạt hơn
                $data = $this->extractJsonObject($text);
                if ($data && is_array($data)) {
                    Log::info('[Plan D] extractPreferencesWithAi OK', $data);
                    return array_merge($default, $data);
                }
            }
            Log::warning('[Plan D] extractPreferencesWithAi failed, status=' . $response->status());
        } catch (\Exception $e) {
            Log::error('[Plan D] extractPreferencesWithAi exception: ' . $e->getMessage());
        }

        return $default;
    }

    private function extractJsonObject($text)
    {
        $text = preg_replace('/```(?:json)?|```/', '', $text);
        $text = trim($text);
        $startPos = strpos($text, '{');
        $endPos   = strrpos($text, '}');
        if ($startPos !== false && $endPos !== false) {
            return json_decode(substr($text, $startPos, $endPos - $startPos + 1), true);
        }
        return null;
    }
}
