<?php

namespace App\Services;

use App\Models\DiaDiem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * RecommendationService
 *
 * Thuật toán Lai (Hybrid) tối ưu hóa lịch trình du lịch:
 * 1. Rule-based: Ràng buộc về ngân sách, khoảng cách địa lý, khung giờ (Sáng/Trưa/Chiều/Tối).
 * 2. Content-based: Chấm điểm dựa trên sở thích người dùng (Preferences) và Metadata địa điểm.
 * 3. Fallback: Đảm bảo lịch trình không quá dày đặc bằng cách thêm các khoảng "Nghỉ ngơi tự do".
 */
class RecommendationService
{
    private array $userPreferences = [];
    private float $dailyBudget = 0;
    private int $soNguoi = 1;
    private array $spendingProfile = [];
    /** Khi true: chỉ chọn địa điểm miễn phí (gia_ve = 0) hoặc thời gian tự do */
    private bool $forceFreeOnly = false;

    /**
     * Entry point để lấy lịch trình.
     * Tương thích hoàn toàn với AiController hiện tại.
     *
     * @param array $preferences Danh sách từ khóa sở thích
     * @param int $days Số ngày đi du lịch
     * @param float $budget Tổng ngân sách
     * @param int $soNguoi Số lượng người
     * @param string $startDate Ngày bắt đầu
     * @return array Mảng 2 chiều chứa lịch trình từng ngày
     */
    public function getRecommendedItinerary(array $preferences, int $days, float $budget, int $soNguoi, string $startDate, array $weatherData = []): array
    {
        Log::info('RecommendationService: Khởi chạy Advanced Scoring Engine (V2)', [
            'days' => $days, 'budget' => $budget, 'so_nguoi' => $soNguoi, 'preferences' => $preferences
        ]);

        $this->userPreferences = $preferences;
        $this->soNguoi = max(1, $soNguoi);
        $this->dailyBudget = $days > 0 ? ($budget / $days) : 0;
        $this->spendingProfile = [];

        $places = $this->fetchCandidatePlaces($preferences);

        return $this->buildSchedule($places, $days, $startDate, $weatherData);
    }

    /**
     * Entry point dành cho Tái Tối Ưu Lịch Trình dựa trên chi phí thực tế.
     * - Loại trừ địa điểm đã đi (usedIds)
     * - Áp dụng spending profile để điều chỉnh trọng số calculateScore()
     *
     * @param array $preferences   Sở thích người dùng (giữ nguyên từ lịch trình gốc)
     * @param int $days            Số ngày còn lại chưa đi
     * @param float $budget        Ngân sách thực còn lại
     * @param int $soNguoi         Số người
     * @param string $startDate    Ngày bắt đầu tái tối ưu
     * @param array $usedIds       Danh sách id địa điểm đã đi rồi (để loại trừ)
     * @param array $spendingProfile Kết quả từ analyzeSpendingProfile()
     * @param array $weatherData   Dữ liệu thời tiết
     * @return array Lịch trình mới
     */
    public function getRecommendedItineraryReOptimize(
        array $preferences,
        int $days,
        float $budget,
        int $soNguoi,
        string $startDate,
        array $usedIds = [],
        array $spendingProfile = [],
        array $weatherData = []
    ): array {
        Log::info('RecommendationService: Tái Tối Ưu Lịch Trình (Budget Re-Optimize)', [
            'days'             => $days,
            'budget'           => $budget,
            'so_nguoi'         => $soNguoi,
            'usedIds_count'    => count($usedIds),
            'spending_profile' => $spendingProfile,
        ]);

        $this->userPreferences = $preferences;
        $this->soNguoi         = max(1, $soNguoi);
        $this->spendingProfile = $spendingProfile;
        $this->forceFreeOnly   = false;

        // ─── Xác định chế độ ngân sách dựa trên ngân sách còn lại ────────────────
        if ($budget <= 0) {
            // Không còn ngân sách: chỉ chọn địa điểm miễn phí
            $this->forceFreeOnly             = true;
            $this->spendingProfile['budget_mode']    = true;
            $this->spendingProfile['force_free_only'] = true;
            $effectiveBudget = 0;
            Log::info('[ReOptimize] Ngân sách đã cạn — bật FREE-ONLY mode');
        } elseif (!empty($spendingProfile['budget_mode'])) {
            // Vượt budget > 20%: giảm mạnh ngân sách còn lại xuống 50%
            $effectiveBudget = $budget * 0.5;
            Log::info('[ReOptimize] Budget mode ON — hiệu lực 50% ngân sách còn lại', [
                'original' => $budget, 'effective' => $effectiveBudget
            ]);
        } elseif (!empty($spendingProfile['is_over_budget'])) {
            // Vượt budget nhẹ (< 20%): giảm xuống 70%
            $effectiveBudget = $budget * 0.7;
            Log::info('[ReOptimize] Over budget (nhẹ) — hiệu lực 70% ngân sách còn lại', [
                'original' => $budget, 'effective' => $effectiveBudget
            ]);
        } else {
            // Bình thưỜng: dùng đầy đủ ngân sách còn lại
            $effectiveBudget = $budget;
        }

        // Kể cả khi effectiveBudget = 0, vẫn đặt dailyBudget = 1 để logic filter vẫn hoạt động
        $this->dailyBudget = $days > 0
            ? max($this->forceFreeOnly ? 0.01 : 0, $effectiveBudget / $days)
            : 0.01; // Tạo giá trị nhỏ dương để filter không bị skip

        $places = $this->fetchCandidatePlaces($preferences);

        // Loại trừ địa điểm đã đi
        if (!empty($usedIds)) {
            $places = $places->reject(fn($p) => in_array($p->id, $usedIds));
        }

        // FREE-ONLY mode: loại trừ toàn bộ địa điểm có vé trước khi chạy thuật toán
        if ($this->forceFreeOnly) {
            $places = $places->filter(fn($p) => ($p->gia_ve ?? 0) == 0);
            Log::info('[ReOptimize] FREE-ONLY: chỉ còn ' . $places->count() . ' địa điểm miễn phí');
        }

        return $this->buildSchedule($places, $days, $startDate, $weatherData);
    }

    /**
     * Phân tích hành vi chi tiêu từ danh sách chi phí phát sinh.
     * Trả về spending_profile để điều chỉnh trọng số calculateScore().
     *
     * @param array $chiPhis Mảng chi_phi_phat_sinhs dạng [{ loai_chi_phi, tong_chi_phi }, ...]
     * @return array spending_profile
     */
    public static function analyzeSpendingProfile(array $chiPhis, float $originalBudget, float $estimatedSpent): array
    {
        if (empty($chiPhis)) {
            return [
                'food_ratio'            => 0,
                'shopping_ratio'        => 0,
                'is_over_budget'        => false,
                'budget_mode'           => false,
                'total_incurred'        => 0,
            ];
        }

        $total         = 0;
        $foodTotal     = 0;
        $shoppingTotal = 0;

        foreach ($chiPhis as $cp) {
            $amount = (float)($cp['tong_chi_phi'] ?? 0);
            $total += $amount;
            $loai   = mb_strtolower((string)($cp['loai_chi_phi'] ?? ''));

            if (in_array($loai, ['ăn uống', 'an uong'])) {
                $foodTotal += $amount;
            } elseif (in_array($loai, ['mua sắm', 'mua sam'])) {
                $shoppingTotal += $amount;
            }
        }

        $foodRatio     = $total > 0 ? round($foodTotal / $total, 2) : 0;
        $shoppingRatio = $total > 0 ? round($shoppingTotal / $total, 2) : 0;

        // Tổng chi tiêu (ước tính + phát sinh) vs ngân sách gốc
        $totalSpent    = $estimatedSpent + $total;
        $isOverBudget  = $originalBudget > 0 && $totalSpent > $originalBudget;
        // Budget mode khi vượt > 10% (giảm ngưỡng từ 20% xuống 10% để nhạy hơn)
        $budgetMode    = $originalBudget > 0 && $totalSpent > ($originalBudget * 1.1);

        Log::info('[analyzeSpendingProfile]', [
            'total_incurred'  => $total,
            'food_ratio'      => $foodRatio,
            'shopping_ratio'  => $shoppingRatio,
            'is_over_budget'  => $isOverBudget,
            'budget_mode'     => $budgetMode,
        ]);

        return [
            'food_ratio'     => $foodRatio,
            'shopping_ratio' => $shoppingRatio,
            'is_over_budget' => $isOverBudget,
            'budget_mode'    => $budgetMode,
            'total_incurred' => $total,
        ];
    }

    /**
     * Truy xuất dữ liệu nguồn ban đầu để thuật toán phân loại.
     */
    private function fetchCandidatePlaces(array $preferences): Collection
    {
        $query = DiaDiem::query();

        if (!empty($preferences)) {
            $query->where(function ($q) use ($preferences) {
                foreach ($preferences as $pref) {
                    $q->orWhere('loai_dia_diem', 'LIKE', "%$pref%")
                        ->orWhere('mo_ta', 'LIKE', "%$pref%")
                        ->orWhere('ten_dia_diem', 'LIKE', "%$pref%");
                }
            });
        }

        // Ưu tiên lấy các địa điểm rating tốt để đảm bảo chất lượng, lấy số lượng đủ lớn để gom cụm
        $places = $query->orderByDesc('danh_gia_trung_binh')->limit(200)->get();

        // Nếu tập kết quả lọc theo sở thích quá ít (dưới 30), fallback lấy thêm dữ liệu chung
        if ($places->count() < 30) {
            $places = DiaDiem::orderByDesc('danh_gia_trung_binh')->limit(150)->get();
        }

        return $places;
    }

    /**
     * Thuật toán tự động xếp lịch với Ngân sách theo ngày và Format chuẩn.
     */
    private function buildSchedule(Collection $places, int $days, string $startDate = '', array $weatherData = []): array
    {
        $itinerary = [];
        $usedIds   = [];

        // Index weather by date for O(1) lookup: ['YYYY-MM-DD' => 'rain'|'sunny'|'partly_cloudy'|'cloudy']
        $weatherByDate = [];
        foreach ($weatherData as $w) {
            if (!empty($w['date']) && !empty($w['condition'])) {
                $weatherByDate[$w['date']] = $w['condition'];
            }
        }

        $slotTemplates = [
            [
                'min_time' => '06:30', 'max_time' => '08:00', 'window_key' => 'breakfast', 'label' => 'Ăn sáng', 'is_food' => true, 'is_rest' => false,
                'keywords'  => ['ăn sáng', 'bún', 'phở', 'mì', 'bánh mì', 'xôi', 'cháo', 'quán ăn', 'ẩm thực'],
                'must_have' => ['phở', 'bún', 'mì', 'bánh mì', 'xôi', 'cháo', 'café sáng', 'điểm tâm', 'quán ăn sáng'],
                'forbidden' => ['kem', 'chè', 'trà sữa', 'bánh ngọt', 'lẩu', 'bar', 'pub', 'bbq'],
            ],
            [
                'min_time' => '08:00', 'max_time' => '09:30', 'window_key' => 'morning', 'label' => 'Cafe/Check-in', 'is_food' => false, 'is_rest' => false,
                'keywords'  => ['cafe', 'cà phê', 'check-in', 'sống ảo', 'chill', 'chụp ảnh'],
                'must_have' => [], 'forbidden' => [],
            ],
            [
                'min_time' => '09:00', 'max_time' => '12:00', 'window_key' => 'morning', 'label' => 'Tham quan sáng', 'is_food' => false, 'is_rest' => false,
                'keywords'  => ['biển', 'tâm linh', 'chùa', 'bảo tàng', 'công viên', 'di tích', 'tham quan', 'thiên nhiên'],
                'must_have' => [], 'forbidden' => [],
            ],
            [
                'min_time' => '11:30', 'max_time' => '13:30', 'window_key' => 'lunch', 'label' => 'Ăn trưa', 'is_food' => true, 'is_rest' => false,
                'keywords'  => ['nhà hàng', 'quán ăn', 'ẩm thực', 'cơm', 'hải sản'],
                'must_have' => ['nhà hàng', 'quán ăn', 'cơm', 'đặc sản', 'hải sản'],
                'forbidden' => ['kem', 'chè', 'trà sữa', 'bánh ngọt'],
            ],
            [
                'min_time' => '13:30', 'max_time' => '15:00', 'window_key' => 'rest', 'label' => 'Nghỉ ngơi', 'is_food' => false, 'is_rest' => true,
                'keywords' => [],
            ],
            [
                'min_time' => '15:00', 'max_time' => '17:30', 'window_key' => 'afternoon', 'label' => 'Tham quan chiều', 'is_food' => false, 'is_rest' => false,
                'keywords'  => ['biển', 'tâm linh', 'chùa', 'bảo tàng', 'công viên', 'di tích', 'tham quan', 'vui chơi'],
                'must_have' => [], 'forbidden' => [],
            ],
            [
                'min_time' => '15:30', 'max_time' => '17:30', 'window_key' => 'afternoon', 'label' => 'Ăn vặt/Cafe chiều', 'is_food' => false, 'is_rest' => false,
                'keywords'  => ['kem', 'chè', 'trà sữa', 'cafe', 'cà phê', 'bánh ngọt', 'snack', 'ăn vặt'],
                'must_have' => ['kem', 'chè', 'trà sữa', 'café', 'bánh ngọt', 'snack'],
                'forbidden' => [],
            ],
            [
                'min_time' => '17:30', 'max_time' => '20:00', 'window_key' => 'dinner', 'label' => 'Ăn tối', 'is_food' => true, 'is_rest' => false,
                'keywords'  => ['nhà hàng', 'quán ăn', 'hải sản', 'nướng', 'lẩu', 'ẩm thực', 'bbq'],
                'must_have' => ['nhà hàng', 'quán ăn', 'hải sản', 'bbq', 'lẩu', 'ẩm thực'],
                'forbidden' => ['kem', 'chè', 'trà sữa', 'bánh ngọt'],
            ],
            [
                'min_time' => '19:30', 'max_time' => '22:00', 'window_key' => 'night', 'label' => 'Quán nhậu/Streetfood tối', 'is_food' => true, 'is_rest' => false,
                'keywords'  => ['quán nhậu', 'nhậu', 'bia', 'bia tươi', 'hải sản vỉa hè', 'ăn vặt', 'bánh tráng', 'nem', 'streetfood', 'bún chả', 'lò mò'],
                'must_have' => ['quán nhậu', 'nhậu', 'bia', 'hải sản', 'ăn vặt', 'streetfood'],
                'forbidden' => [],
            ],
            [
                'min_time' => '20:00', 'max_time' => '22:30', 'window_key' => 'night', 'label' => 'Dạo tối', 'is_food' => false, 'is_rest' => false,
                'keywords'  => ['cầu', 'chợ đêm', 'phố đi bộ', 'cafe đêm', 'bar', 'pub', 'giải trí', 'tối', 'đêm', 'âm nhạc', 'acoustic'],
                'must_have' => [], 'forbidden' => [],
            ],
        ];

        $districtCenters = [
            ['name' => 'Hải Châu / Thanh Khê (Trung tâm)', 'lat' => 16.0611, 'lng' => 108.2274],
            ['name' => 'Sơn Trà (Biển / Bán đảo)',          'lat' => 16.0820, 'lng' => 108.2440],
            ['name' => 'Ngũ Hành Sơn (Biển / Non Nước)',    'lat' => 16.0150, 'lng' => 108.2520],
            ['name' => 'Cẩm Lệ / Hòa Vang (Ngoại ô)',      'lat' => 16.0100, 'lng' => 108.2000],
            ['name' => 'Liên Chiểu / Vịnh Đà Nẵng',        'lat' => 16.0880, 'lng' => 108.1500],
        ];
        shuffle($districtCenters);

        for ($d = 0; $d < $days; $d++) {
            // Xác định ngày thực tế (để check Thứ 6/Thứ 7 cho Cầu Rồng)
            $currentDate = '';
            $dayOfWeek   = -1;
            if ($startDate) {
                $ts          = strtotime($startDate) + $d * 86400;
                $currentDate = date('Y-m-d', $ts);
                $dayOfWeek   = (int)date('N', $ts); // 1=Mon…5=Fri,6=Sat,7=Sun
            }

            // Xác định thời tiết hôm đó
            $todayWeather = $weatherByDate[$currentDate] ?? 'sunny';
            $isRainy      = in_array($todayWeather, ['rain', 'cloudy']);

            $centerIndex    = $d % count($districtCenters);
            $dailyCenterLat = $districtCenters[$centerIndex]['lat'];
            $dailyCenterLng = $districtCenters[$centerIndex]['lng'];

            $daySchedule     = [];
            $currentDaySpent = 0;
            $currentLat      = $dailyCenterLat;
            $currentLng      = $dailyCenterLng;
            $currentEndMin   = $this->timeToMinutes('06:30');

            // ── Cầu Rồng phun lửa: chỉ Thứ 6 (5) và Thứ 7 (6) lúc 21:00 ──
            $hasDragonBridge = ($dayOfWeek === 5 || $dayOfWeek === 6);

            foreach ($slotTemplates as $slot) {
                $minStartMin    = $this->timeToMinutes($slot['min_time']);
                $maxEndMin      = $this->timeToMinutes($slot['max_time']);
                $travelMin      = 12;
                $actualStartMin = max($minStartMin, $currentEndMin + $travelMin);
                $latestStartMin = max($minStartMin, $maxEndMin - 15);
                if ($actualStartMin > $latestStartMin) {
                    $actualStartMin = $latestStartMin;
                }
                $actualStartTime = $this->minutesToTime($actualStartMin);

                // Slot nghỉ ngơi
                if (!empty($slot['is_rest'])) {
                    $restDuration  = max(15, min(75, $maxEndMin - $actualStartMin));
                    $currentEndMin = $actualStartMin + $restDuration;
                    $daySchedule[] = $this->createFreeTimeSlot($actualStartTime, $slot['label'], $slot['window_key'], $slot['min_time'], $slot['max_time'], $restDuration);
                    continue;
                }

                // Slot đặc biệt Cầu Rồng 21:00 (Thứ 6, Thứ 7) – xếp thay slot Dạo tối
                if ($hasDragonBridge && $slot['window_key'] === 'night' && $slot['label'] === 'Dạo tối') {
                    $dragonStartMin  = $this->timeToMinutes('21:00');
                    $dragonEndMin    = $dragonStartMin + 45;
                    $daySchedule[] = [
                        'id_dia_diem'     => 'dragon_bridge_' . $d,
                        'ten_dia_diem'    => 'Cầu Rồng – Phun lửa',
                        'loai_dia_diem'   => 'Điểm tham quan đêm',
                        'dia_chi'         => 'Cầu Rồng, Quận Sơn Trà, Đà Nẵng',
                        'gio'             => '21:00',
                        'gio_bat_dau'     => '21:00',
                        'gio_ket_thuc'    => '21:45',
                        'thoi_luong_phut' => 45,
                        'ghi_chu'         => 'Cầu Rồng phun lửa & phun nước – đặc sản đêm Đà Nẵng!',
                        'gia_ve'          => 0,
                        'vi_do'           => 16.0610,
                        'kinh_do'         => 108.2271,
                        'image'           => null,
                        'method'          => 'dragon-bridge-special',
                        'window_key'      => 'night',
                        'window_start'    => '21:00',
                        'window_end'      => '22:00',
                    ];
                    $currentEndMin = $dragonEndMin;
                    continue;
                }

                $slotWithTime = array_merge($slot, [
                    'time'    => $actualStartTime,
                    'is_rainy' => $isRainy,
                ]);

                $bestPlace = $this->findBestPlaceForSlot(
                    $places, $usedIds, $currentLat, $currentLng,
                    $dailyCenterLat, $dailyCenterLng, $slotWithTime, $currentDaySpent
                );

                if ($bestPlace) {
                    $usedIds[]        = $bestPlace->id;
                    $currentDaySpent += ($bestPlace->gia_ve ?? 0) * $this->soNguoi;
                    $duration         = max(15, min($this->estimateDuration($bestPlace, $slot), $maxEndMin - $actualStartMin));
                    $currentEndMin    = $actualStartMin + $duration;

                    $daySchedule[] = [
                        'id_dia_diem'     => $bestPlace->id,
                        'ten_dia_diem'    => $bestPlace->ten_dia_diem,
                        'loai_dia_diem'   => $bestPlace->loai_dia_diem ?? '',
                        'dia_chi'         => $bestPlace->dia_chi ?? 'Đà Nẵng',
                        'gio'             => $actualStartTime,
                        'gio_bat_dau'     => $actualStartTime,
                        'gio_ket_thuc'    => $this->minutesToTime($currentEndMin),
                        'thoi_luong_phut' => $duration,
                        'ghi_chu'         => 'Dành cho ' . mb_strtolower($slot['label']),
                        'gia_ve'          => $bestPlace->gia_ve ?? 0,
                        'vi_do'           => $bestPlace->vi_do,
                        'kinh_do'         => $bestPlace->kinh_do,
                        'image'           => $bestPlace->image ?? $bestPlace->hinh_anh ?? null,
                        'method'          => 'algorithm-scoring',
                        'window_key'      => $slot['window_key'],
                        'window_start'    => $slot['min_time'],
                        'window_end'      => $slot['max_time'],
                        // [Plan B] Context bổ sung cho AI prompt
                        'gio_mo_cua'      => $bestPlace->gio_mo_cua   ?? null,
                        'gio_dong_cua'    => $bestPlace->gio_dong_cua ?? null,
                        'thoi_tiet'       => $todayWeather,
                        'ngay_index'      => $d,
                    ];

                    if ($bestPlace->vi_do && $bestPlace->kinh_do) {
                        $currentLat = (float)$bestPlace->vi_do;
                        $currentLng = (float)$bestPlace->kinh_do;
                    }
                } else {
                    $freeDuration  = max(15, min(60, $maxEndMin - $actualStartMin));
                    $currentEndMin = $actualStartMin + $freeDuration;
                    $fallbackLabel = !empty($slot['is_food']) ? 'Tự túc ' . mb_strtolower($slot['label']) : 'Nghỉ ngơi tự do';
                    $daySchedule[] = $this->createFreeTimeSlot($actualStartTime, $fallbackLabel, $slot['window_key'], $slot['min_time'], $slot['max_time'], $freeDuration);
                }
            }

            $daySchedule = $this->optimizeDayRouteNearestNeighborByWindow($daySchedule, $dailyCenterLat, $dailyCenterLng);
            $daySchedule = $this->recalculateDayTimelineByWindow($daySchedule, 12);

            $itinerary[] = $daySchedule;
        }

        return $itinerary;
    }


    /**
     * Tối ưu thứ tự các địa điểm trong ngày bằng Nearest Neighbor heuristic.
     * Chỉ sắp xếp lại các điểm thật (không đụng free-time), giữ nguyên số lượng điểm.
     */
    private function optimizeDayRouteNearestNeighborByWindow(array $daySchedule, float $startLat, float $startLng): array
    {
        $windowOrder = ['breakfast', 'morning', 'lunch', 'rest', 'afternoon', 'dinner', 'night'];
        $currentLat = $startLat;
        $currentLng = $startLng;

        foreach ($windowOrder as $windowKey) {
            $placeIndices = [];
            $candidates = [];

            foreach ($daySchedule as $idx => $item) {
                if (($item['window_key'] ?? null) !== $windowKey) {
                    continue;
                }
                if ($this->isFreeTimeSlot($item)) {
                    continue;
                }

                $placeIndices[] = $idx;
                $candidates[] = $item;
            }

            if (count($candidates) < 2) {
                $lastInWindow = $daySchedule[end($placeIndices)] ?? null;
                if ($lastInWindow && !empty($lastInWindow['vi_do']) && !empty($lastInWindow['kinh_do'])) {
                    $currentLat = (float)$lastInWindow['vi_do'];
                    $currentLng = (float)$lastInWindow['kinh_do'];
                }
                continue;
            }

            $ordered = [];
            while (!empty($candidates)) {
                $bestIdx = 0;
                $bestEffectiveDistance = PHP_FLOAT_MAX;
                $bestRawDistance = PHP_FLOAT_MAX;

                foreach ($candidates as $i => $candidate) {
                    $distance = $this->calculateDistance(
                        $currentLat,
                        $currentLng,
                        $candidate['vi_do'] ?? null,
                        $candidate['kinh_do'] ?? null
                    );

                    // Cân bằng: vẫn ưu tiên gần, nhưng giảm nhẹ điểm phạt nếu hợp sở thích.
                    $preferenceBonusKm = $this->hasPreferenceMatch($candidate) ? 0.35 : 0.0;
                    $effectiveDistance = max(0.0, $distance - $preferenceBonusKm);

                    if (
                        $effectiveDistance < $bestEffectiveDistance ||
                        ($effectiveDistance === $bestEffectiveDistance && $distance < $bestRawDistance)
                    ) {
                        $bestIdx = $i;
                        $bestEffectiveDistance = $effectiveDistance;
                        $bestRawDistance = $distance;
                    }
                }

                $next = $candidates[$bestIdx];
                $ordered[] = $next;

                if (!empty($next['vi_do']) && !empty($next['kinh_do'])) {
                    $currentLat = (float)$next['vi_do'];
                    $currentLng = (float)$next['kinh_do'];
                }

                array_splice($candidates, $bestIdx, 1);
            }

            foreach ($placeIndices as $i => $idx) {
                $daySchedule[$idx] = $ordered[$i];
            }
        }

        return $daySchedule;
    }

    /**
     * Tính lại mốc giờ sau khi tối ưu thứ tự để lịch trình vẫn liền mạch.
     */
    private function recalculateDayTimelineByWindow(array $daySchedule, int $defaultTravelMin = 12): array
    {
        $windowOrder = ['breakfast', 'morning', 'lunch', 'rest', 'afternoon', 'dinner', 'night'];

        foreach ($windowOrder as $windowKey) {
            $indices = [];
            foreach ($daySchedule as $idx => $item) {
                if (($item['window_key'] ?? null) === $windowKey) {
                    $indices[] = $idx;
                }
            }

            if (empty($indices)) {
                continue;
            }

            $firstItem = $daySchedule[$indices[0]];
            $windowStart = $this->timeToMinutes($firstItem['window_start'] ?? '06:30');
            $windowEnd   = $this->timeToMinutes($firstItem['window_end']   ?? '23:59');
            $currentInWindow = $windowStart;
            $prevLat = null;
            $prevLng = null;

            foreach ($indices as $pos => $idx) {
                $item     = $daySchedule[$idx];
                $duration = max(15, (int)($item['thoi_luong_phut'] ?? 60));

                // Tính thời gian di chuyển động dựa trên khoảng cách Haversine
                $dynamicTravel = $defaultTravelMin;
                if ($pos > 0 && $prevLat !== null && $prevLng !== null
                    && !empty($item['vi_do']) && !empty($item['kinh_do'])) {
                    $distKm = $this->calculateDistance($prevLat, $prevLng, $item['vi_do'], $item['kinh_do']);
                    // Xe máy Đà Nẵng ~20 km/h + 5 phút buffer, tối thiểu 5, tối đa 40 phút
                    $dynamicTravel = max(5, min(40, (int)ceil($distKm / 20 * 60) + 5));
                }

                $startMin = ($pos === 0) ? $currentInWindow : $currentInWindow + $dynamicTravel;
                $startMin = max($startMin, $windowStart);
                if ($startMin > ($windowEnd - 15)) {
                    $startMin = $windowEnd - 15;
                }

                $duration = min($duration, max(15, $windowEnd - $startMin));
                $endMin   = min($windowEnd, $startMin + $duration);
                $duration = max(15, $endMin - $startMin);

                $start = $this->minutesToTime($startMin);
                $end   = $this->minutesToTime($endMin);

                $daySchedule[$idx]['gio']             = $start;
                $daySchedule[$idx]['gio_bat_dau']     = $start;
                $daySchedule[$idx]['gio_ket_thuc']    = $end;
                $daySchedule[$idx]['thoi_luong_phut'] = $duration;

                $currentInWindow = $endMin;
                $prevLat = !empty($item['vi_do'])   ? (float)$item['vi_do']   : $prevLat;
                $prevLng = !empty($item['kinh_do']) ? (float)$item['kinh_do'] : $prevLng;
            }
        }

        return $daySchedule;
    }

    /**
     * Free-time slot được tạo bởi hệ thống, không thuộc tập địa điểm thật.
     */
    private function isFreeTimeSlot(array $item): bool
    {
        if (($item['loai_dia_diem'] ?? '') === 'Nghỉ ngơi') {
            return true;
        }

        $id = (string)($item['id_dia_diem'] ?? '');
        if (str_starts_with($id, 'free_time_')) {
            return true;
        }

        return (($item['method'] ?? '') === 'system-fallback');
    }

    /**
     * Ước lượng địa điểm có match sở thích người dùng hay không.
     */
    private function hasPreferenceMatch(array $item): bool
    {
        if (empty($this->userPreferences)) {
            return false;
        }

        $metadata = mb_strtolower(
            (($item['ten_dia_diem'] ?? '') . ' ') .
            (($item['loai_dia_diem'] ?? '') . ' ') .
            (($item['ghi_chu'] ?? ''))
        );

        foreach ($this->userPreferences as $pref) {
            if ($pref !== '' && mb_strpos($metadata, mb_strtolower((string)$pref)) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Tìm địa điểm tốt nhất cho slot dùng 3-pass Cluster Radius:
     * Pass 1: chỉ tìm trong bán kính 3 km (gom cụm chặt)
     * Pass 2: mở rộng lên 8 km
     * Pass 3: không giới hạn khoảng cách (fallback toàn bộ)
     */
    private function findBestPlaceForSlot(Collection $places, array $usedIds, $currentLat, $currentLng, $dailyCenterLat, $dailyCenterLng, array $slot, float $currentDaySpent)
    {
        $remainingDailyBudget       = ($this->dailyBudget > 0) ? ($this->dailyBudget - $currentDaySpent) : PHP_FLOAT_MAX;
        $maxAcceptableTicketPerPerson = $remainingDailyBudget / $this->soNguoi;

        $radiusPasses = [3.0, 8.0, PHP_FLOAT_MAX];

        foreach ($radiusPasses as $maxRadius) {
            $bestPlace    = null;
            $highestScore = -999999;

            foreach ($places as $place) {
                if (in_array($place->id, $usedIds)) continue;

                $giaVe = $place->gia_ve ?? 0;

                // ✅ HARD FILTER: forceFreeOnly — bỏ qua toàn bộ địa điểm có vé, dù calculateScore có chạy
                if ($this->forceFreeOnly && $giaVe > 0) continue;

                // Filter ngân sách bình thường: bỏ qua địa điểm vượt ngân sách ngày
                if ($this->dailyBudget > 0 && $giaVe > $maxAcceptableTicketPerPerson) continue;

                // Giới hạn khoảng cách trong pass này (so với currentLat)
                if ($maxRadius < PHP_FLOAT_MAX) {
                    $dist = $this->calculateDistance($currentLat, $currentLng, $place->vi_do, $place->kinh_do);
                    if ($dist > $maxRadius) continue;
                }

                $score = $this->calculateScore($place, $currentLat, $currentLng, $dailyCenterLat, $dailyCenterLng, $slot, $remainingDailyBudget);

                // Ưu tiên gần nhất trong các lựa chọn hợp lệ (so với điểm hiện tại)
                $distance = $this->calculateDistance($currentLat, $currentLng, $place->vi_do, $place->kinh_do);
                $score -= ($distance * 6); // Tăng độ nhạy với khoảng cách

                // Phạt nhẹ nếu bắt đầu trôi quá xa khỏi tâm của ngày (để giữ chân trong quận)
                $distFromDailyCenter = $this->calculateDistance($dailyCenterLat, $dailyCenterLng, $place->vi_do, $place->kinh_do);
                if ($distFromDailyCenter > 5.0) {
                    $score -= ($distFromDailyCenter * 3); // Càng xa tâm ngày càng trừ nặng
                }

                if ($distance <= 1.5) {
                    $score += 12; // Thưởng thêm cho địa điểm rất gần
                } elseif ($distance <= 3.0) {
                    $score += 6; // Thưởng cho địa điểm gần
                }

                if ($score > $highestScore) {
                    $highestScore = $score;
                    $bestPlace    = $place;
                }
            }

            // Nếu pass này tìm được (và đủ tốt) thì dùng luon, không cần mở rộng thêm
            if ($bestPlace !== null && $highestScore > -50) {
                return $bestPlace;
            }
        }

        return null;
    }

    /**
     * Scoring Engine: Hàm ra quyết định (Decision Making) dựa trên trọng số
     * 
     * Quy tắc ăn uống bắt buộc:
     * - 06:30-08:00: Phở, bún, mì, bánh mì, xôi, cháo - CẤM kem, lẩu, bar, hải sản tối
     * - 11:30-13:30: Nhà hàng, cơm, đặc sản - Tránh nắng gắt nếu có lựa chọn khác
     * - 15:30-17:30: Kem, chè, trà sữa, café, bánh ngọt
     * - 18:00-20:00: Hải sản, BBQ, lẩu, nhà hàng - CẤM kem, chè
     * - 20:00-21:30: Chợ đêm, cầu, phố đi bộ, café đêm, bar nhẹ
     */
    private function calculateScore($place, $currentLat, $currentLng, $dailyCenterLat, $dailyCenterLng, array $slot, float $remainingDailyBudget): float
    {
        $score = 0;

        // Gom toàn bộ text để search keyword
        // [Tầng 3 - Spending Profile] Áp dụng trước khi scoring chính
        $giaVeCheck  = (float)($place->gia_ve ?? 0);
        $foodRatio   = $this->spendingProfile['food_ratio']    ?? 0;
        $shopRatio   = $this->spendingProfile['shopping_ratio'] ?? 0;
        $budgetMode  = $this->spendingProfile['budget_mode']   ?? false;
        $metaCheck   = mb_strtolower($place->loai_dia_diem . ' ' . $place->ten_dia_diem . ' ' . $place->mo_ta);

        // Budget Mode: không chọn bất kỳ địa điểm nào có vé (kể cả vé nhỏ)
        if ($budgetMode && $giaVeCheck > 0) {
            return -9999; // Chế độ tiết kiệm: chỉ chọn địa điểm miễn phí
        }

        // Chi ăn uống > 50%: tăng penalty cho nhà hàng đắt, thưởng street food
        if ($foodRatio > 0.50) {
            if ($giaVeCheck > 200000 && (
                mb_strpos($metaCheck, 'nhà hàng') !== false ||
                mb_strpos($metaCheck, 'hải sản') !== false
            )) {
                $score -= 25; // Phạt nhà hàng đắt khi đã chi nhiều cho ăn uống
            }
            if (
                mb_strpos($metaCheck, 'street food') !== false ||
                mb_strpos($metaCheck, 'quán ăn') !== false ||
                mb_strpos($metaCheck, 'bình dân') !== false ||
                mb_strpos($metaCheck, 'chay') !== false
            ) {
                $score += 20; // Thưởng quán bình dân khi chi ăn nhiều
            }
        }

        // Chi mua sắm > 30%: giảm điểm địa điểm có vé, thưởng miễn phí
        if ($shopRatio > 0.30) {
            if ($giaVeCheck > 0) {
                $score -= 20; // Phạt địa điểm có vé khi đã chi nhiều cho mua sắm
            } else {
                $score += 15; // Thưởng thêm địa điểm miễn phí
            }
        }
        $metadata = mb_strtolower($place->loai_dia_diem . ' ' . $place->mo_ta . ' ' . $place->ten_dia_diem);

        // ============ PHẦN 1: KIỂM TRA LUẬT CẤM TUYỆT ĐỐI ============
        // Nếu vi phạm quy tắc cấm, trả về -9999 (không được chọn tuyệt đối)
        $forbiddenResult = $this->checkForbiddenRules($metadata, $slot);
        if ($forbiddenResult === -9999) {
            return -9999; // Phạt chết
        }

        // ============ PHẦN 2: MATCH KHUNG GIỜ / LOẠI HÌNH BẮT BUỘC (CRITICAL RULE) ============
        $slotMatch = false;
        foreach ($slot['keywords'] as $kw) {
            if (mb_strpos($metadata, mb_strtolower($kw)) !== false) {
                $slotMatch = true;
                break;
            }
        }

        if ($slotMatch) {
            $score += 60; // Thưởng cực lớn để ép đúng loại (Vd: Trưa phải đi Ăn)
        } else {
            // Nếu slot yêu cầu là chỗ ăn uống nhưng địa điểm không phải chỗ ăn -> Cấm tuyệt đối
            if ($slot['is_food']) {
                return -9999; // Phạt chết
            }
            $score -= 40; // Trừ nặng nếu lệch ngữ cảnh
        }

        // ============ PHẦN 3: KIỂM TRA MUST_HAVE KEYWORDS (Yêu cầu bắt buộc) ============
        if (!empty($slot['must_have'])) {
            $hasMustKeyword = false;
            foreach ($slot['must_have'] as $must_kw) {
                if (mb_strpos($metadata, mb_strtolower($must_kw)) !== false) {
                    $hasMustKeyword = true;
                    $score += 40; // Thưởng thêm vì match must_have
                    break;
                }
            }

            // Nếu là slot ăn uống yêu cầu must_have mà không match -> Phạt nặng
            if ($slot['is_food'] && !$hasMustKeyword) {
                $score -= 80; // Phạt vì không match must_have keywords
            }
        }

        // ============ PHẦN 4: MATCH SỞ THÍCH NGƯỜI DÙNG (+30) ============
        foreach ($this->userPreferences as $pref) {
            if (mb_strpos($metadata, mb_strtolower($pref)) !== false) {
                $score += 30;
                break; // Chỉ tính thưởng 1 lần để tránh lạm phát điểm
            }
        }

        // ============ PHẦN 5: RATING SCORE (+danh_gia * 8) ============
        $rating = $place->danh_gia_trung_binh ?? 3.5;
        $score += ($rating * 8);

        // ============ PHẦN 6: KIỂM SOÁT NGÂN SÁCH & ƯU TIÊN GIÁ RẺ ============
        $giaVe = $place->gia_ve ?? 0;

        if ($giaVe == 0) {
            $score += 20; // Ưu tiên điểm miễn phí
        }

        if ($this->dailyBudget > 0 && $giaVe > 0) {
            $ticketTotal = $giaVe * $this->soNguoi;

            // Nếu ngân sách ngày còn dưới 30%, hệ thống chuyển sang chế độ "Thắt lưng buộc bụng"
            if ($remainingDailyBudget < ($this->dailyBudget * 0.3)) {
                $score -= 30; // Trừ nặng các chỗ tốn tiền
            } else {
                // Chiếm phần lớn ngân sách còn lại -> Phạt nhẹ để hạn chế
                $percent = $ticketTotal / $remainingDailyBudget;
                if ($percent > 0.5) {
                    $score -= 15;
                }
            }
        }

        // ============ PHẦN 7: GOM CỤM ĐỊA LÝ & KHOẢNG CÁCH ============
        $distance = $this->calculateDistance($currentLat, $currentLng, $place->vi_do, $place->kinh_do);

        if ($distance < 1.5) {
            $score += 35; // Ưu tiên rất gần để giảm di chuyển
        } elseif ($distance < 3.0) {
            $score += 18; // Khoảng cách ngắn: giữ tuyến đường hợp lý
        } elseif ($distance < 6) {
            $score += 6; // Khoảng cách di chuyển hợp lý
        }

        // Phạt di chuyển xa hơn: tăng trọng số để ưu tiên lộ trình ngắn.
        $score -= ($distance * 7);

        // ============ PHẦN 8: ƯU TIÊN THEO HÀNH VI DU LỊCH THỰC TẾ ============
        $windowKey = $slot['window_key'] ?? '';
        
        // Chùa / tâm linh: ưu tiên sáng (+15), chiều chấp nhận (-5)
        if (mb_strpos($metadata, 'chùa') !== false || mb_strpos($metadata, 'tâm linh') !== false) {
            if ($windowKey === 'morning') {
                $score += 15;
            } elseif ($windowKey === 'afternoon') {
                $score -= 5;
            }
        }

        // Biển / ngoài trời: hạn chế tối (-15)
        if (mb_strpos($metadata, 'biển') !== false) {
            if ($windowKey === 'dinner' || $windowKey === 'night') {
                $score -= 15;
            }
        }
        

        // Kem / chè / trà sữa: ưu tiên chiều/tối (+15)
        if (mb_strpos($metadata, 'kem') !== false || mb_strpos($metadata, 'chè') !== false || mb_strpos($metadata, 'trà sữa') !== false) {
            if ($windowKey === 'afternoon' || $windowKey === 'night') {
                $score += 15;
            }
        }

        // ============ PHẦN 9: THỜI TIẾT – Ưu tiên địa điểm phù hợp ============
        $isRainy = !empty($slot['is_rainy']);
        $isOutdoor = mb_strpos($metadata, 'biển') !== false
            || mb_strpos($metadata, 'công viên') !== false
            || mb_strpos($metadata, 'bãi tắm') !== false
            || mb_strpos($metadata, 'ngoài trời') !== false;
        $isIndoor = mb_strpos($metadata, 'bảo tàng') !== false
            || mb_strpos($metadata, 'cafe') !== false
            || mb_strpos($metadata, 'cà phê') !== false
            || mb_strpos($metadata, 'nhà hàng') !== false
            || mb_strpos($metadata, 'quán ăn') !== false
            || mb_strpos($metadata, 'trung tâm') !== false;

        if ($isRainy) {
            if ($isIndoor)  { $score += 30; } // Ưu tiên trong nhà khi mưa
            if ($isOutdoor) { $score -= 40; } // Phạt nặng ngoài trời khi mưa
        } else {
            if ($isOutdoor) { $score += 15; } // Khuyến khích ra ngoài trời khi nắng
        }

        return $score;
    }

    /**
     * Kiểm tra các quy tắc cấm trong từng khung giờ
     * Trả về -9999 nếu vi phạm quy tắc cấm tuyệt đối
     */
    private function checkForbiddenRules(string $metadata, array $slot): int
    {
        $time = null;
        if (array_key_exists('time', $slot)) {
            $time = $slot['time'];
        } elseif (array_key_exists('min_time', $slot)) {
            $time = $slot['min_time'];
        }
        $windowKey = $slot['window_key'] ?? null;

        $isChua = mb_strpos($metadata, 'chùa') !== false || mb_strpos($metadata, 'tâm linh') !== false;
        $isMusic = mb_strpos($metadata, 'bar') !== false || mb_strpos($metadata, 'âm nhạc') !== false || mb_strpos($metadata, 'acoustic') !== false || mb_strpos($metadata, 'chợ đêm') !== false;
        $isSweet = mb_strpos($metadata, 'kem') !== false || mb_strpos($metadata, 'chè') !== false || mb_strpos($metadata, 'trà sữa') !== false;

        // 1. Chùa / tâm linh: CẤM tối
        if ($isChua && ($windowKey === 'dinner' || $windowKey === 'night')) {
            return -9999;
        }

        // 6. Kem / chè / trà sữa: không xếp sáng
        if ($isSweet && ($windowKey === 'breakfast' || $windowKey === 'morning')) {
            return -9999;
        }

        // 7. Âm nhạc / bar nhẹ / acoustic / chợ đêm: chỉ buổi tối
        if ($isMusic && $windowKey !== 'night') {
            return -9999;
        }

        // ============ QUY TẮC CẤM 06:30 - 08:30 ============
        if ($windowKey === 'breakfast' || $this->isTimeInRange($time, '06:00', '08:30')) {
            // CẤM lẩu
            if (mb_strpos($metadata, 'lẩu') !== false) {
                return -9999;
            }
            // CẤM hải sản tối (hải sản nướng, hải sản nước xốt)
            if ((mb_strpos($metadata, 'hải sản') !== false && mb_strpos($metadata, 'nướng') !== false) ||
                (mb_strpos($metadata, 'hải sản') !== false && mb_strpos($metadata, 'nước xốt') !== false)
            ) {
                return -9999;
            }
        }

        // ============ QUY TẮC CẤM 12:00 - Trưa (Tránh nắng gắt) ============
        // Nếu là slot trưa, tránh các điểm nắng gắt (phơi nắng)
        if ($windowKey === 'lunch' || $this->isTimeInRange($time, '11:00', '13:30')) {
            $sunnyKeywords = ['biển nắng', 'nắng gắt', 'phơi nắng', 'công viên ngoài trời', 'phá san hô'];
            foreach ($sunnyKeywords as $keyword) {
                if (mb_strpos($metadata, $keyword) !== false) {
                    return -50; // Phạt nặng nhưng không phải cấm tuyệt đối (có thể chọn nếu không có lựa chọn khác)
                }
            }
        }

        return 0; // Không vi phạm quy tắc
    }

    /**
     * Tạo một slot ảo (Nghỉ ngơi tự do) để tương thích với cấu trúc của AiController
     */
    private function createFreeTimeSlot(
        string $time,
        string $label,
        string $windowKey = 'rest',
        string $windowStart = '13:30',
        string $windowEnd = '15:00',
        int $duration = 75
    ): array
    {
        return [
            'id_dia_diem'     => 'free_time_' . uniqid(),
            'ten_dia_diem'    => $label,
            'loai_dia_diem'   => 'Nghỉ ngơi',
            'dia_chi'         => 'Tự do',
            'gio'             => $time,
            'gio_bat_dau'     => $time,
            'gio_ket_thuc'    => $this->addMinutes($time, $duration),
            'thoi_luong_phut' => $duration,
            'ghi_chu'         => 'Nạp lại năng lượng cho hành trình.',
            'gia_ve'          => 0,
            'vi_do'           => null,
            'kinh_do'         => null,
            'image'           => null,
            'method'          => 'system-fallback',
            'window_key'      => $windowKey,
            'window_start'    => $windowStart,
            'window_end'      => $windowEnd
        ];
    }

    /**
     * [Rule-Based + Content-Based] Ước tính thời lượng thực tế (phút) của một địa điểm.
     *
     * Rule-Based : dựa vào nhãn slot (label) đã cấu hình sẵn trong $timeSlots.
     * Content-Based: tinh chỉnh thêm theo từ khóa metadata (loai_dia_diem, ten, mo_ta).
     */
    private function estimateDuration($place, array $slot): int
    {
        $metadata = mb_strtolower(
            ($place->loai_dia_diem ?? '') . ' ' .
            ($place->ten_dia_diem  ?? '') . ' ' .
            ($place->mo_ta         ?? '')
        );
        $label = mb_strtolower($slot['label'] ?? '');

        // ── RULE-BASED theo Slot Label ────────────────────────
        if (str_contains($label, 'ăn sáng'))  return rand(30, 45);
        if (str_contains($label, 'cafe') || str_contains($label, 'check-in')) return rand(30, 60);
        if (str_contains($label, 'ăn vặt') || str_contains($label, 'ăn nhẹ')) return rand(20, 40);
        if (str_contains($label, 'ăn trưa')) return rand(45, 60);
        if (str_contains($label, 'ăn tối') || str_contains($label, 'nhậu') || str_contains($label, 'streetfood')) return rand(60, 90);
        if (str_contains($label, 'nghỉ')) return rand(60, 90);

        // ── CONTENT-BASED theo Metadata ────────────
        $bigPlaces = ['bà nà', 'sơn trà', 'ngũ hành sơn', 'hội an', 'khu du lịch', 'công viên chủ đề'];
        foreach ($bigPlaces as $kw) { if (str_contains($metadata, $kw)) return rand(120, 150); }

        $entertainment = ['vui chơi', 'giải trí', 'trò chơi', 'công viên nước', 'khu vui chơi'];
        foreach ($entertainment as $kw) { if (str_contains($metadata, $kw)) return rand(90, 120); }

        $museums = ['bảo tàng', 'di tích', 'lịch sử', 'cổ viện'];
        foreach ($museums as $kw) { if (str_contains($metadata, $kw)) return rand(75, 90); }

        $outdoor = ['biển', 'bãi tắm', 'khu nghỉ dưỡng'];
        foreach ($outdoor as $kw) { if (str_contains($metadata, $kw)) return rand(90, 120); }

        if (str_contains($metadata, 'công viên')) return rand(60, 90);

        $temples = ['chùa', 'tự viện', 'thánh thất', 'tâm linh', 'lễ hội'];
        foreach ($temples as $kw) { if (str_contains($metadata, $kw)) return rand(60, 75); }

        $markets = ['chợ đêm', 'phố đi bộ', 'thị trường', 'chợ'];
        foreach ($markets as $kw) { if (str_contains($metadata, $kw)) return rand(60, 90); }

        $quick = ['cầu rồng', 'cầu', 'quảng trường', 'cổng', 'tượng'];
        foreach ($quick as $kw) { if (str_contains($metadata, $kw)) return rand(30, 45); }

        $snacks = ['kem', 'chè', 'trà sữa', 'cà phê', 'cafe', 'bánh ngọt', 'snack'];
        foreach ($snacks as $kw) { if (str_contains($metadata, $kw)) return rand(25, 40); }

        $pub = ['quán nhậu', 'nhậu', 'bia', 'bar', 'pub', 'hải sản vỉa hè', 'streetfood'];
        foreach ($pub as $kw) { if (str_contains($metadata, $kw)) return rand(60, 90); }

        $restaurants = ['nhà hàng', 'quán ăn', 'hải sản', 'buffet', 'lẩu', 'nướng'];
        foreach ($restaurants as $kw) { if (str_contains($metadata, $kw)) return rand(45, 60); }

        return rand(60, 90); // Mặc định tham quan
    }

    /**
     * Cộng thêm số phút vào chuỗi giờ HH:MM, trả về chuỗi HH:MM mới.
     */
    private function addMinutes(string $time, int $minutes): string
    {
        [$h, $m] = explode(':', $time);
        $total   = (int)$h * 60 + (int)$m + $minutes;
        $total   = min($total, 23 * 60 + 59); // Không vượt 23:59
        return sprintf('%02d:%02d', intdiv($total, 60), $total % 60);
    }

    /**
     * Chuyển chuỗi HH:MM sang số phút kể từ nửa đêm
     */
    private function timeToMinutes(string $time): int
    {
        [$h, $m] = explode(':', $time);
        return (int)$h * 60 + (int)$m;
    }

    /**
     * Chuyển số phút kể từ nửa đêm sang chuỗi HH:MM
     */
    private function minutesToTime(int $minutes): string
    {
        $minutes = min($minutes, 23 * 60 + 59);
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    /**
     * Ki?m tra m?t m?c HH:MM c� n?m trong [start, end] hay kh�ng.
     */
    private function isTimeInRange(?string $time, string $start, string $end): bool
    {
        if (!$time || strlen($time) < 5) {
            return false;
        }

        $t = $this->timeToMinutes(substr($time, 0, 5));
        $s = $this->timeToMinutes($start);
        $e = $this->timeToMinutes($end);

        return $t >= $s && $t <= $e;
    }

    /**
     * Công thức Haversine tính khoảng cách (km) giữa 2 tọa độ
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return 8.0; // Fallback: Xem như cách 8km để tránh lỗi logic
        }

        $earthRadius = 6371;
        $dLat = deg2rad((float)$lat2 - (float)$lat1);
        $dLon = deg2rad((float)$lon2 - (float)$lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad((float)$lat1)) * cos(deg2rad((float)$lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}

