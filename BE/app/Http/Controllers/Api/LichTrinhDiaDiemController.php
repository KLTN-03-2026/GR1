<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LichTrinhDiaDiem;
use App\Models\DiaDiem;
use App\Http\Requests\StoreLichTrinhDiaDiemRequest;
use App\Http\Requests\UpdateLichTrinhDiaDiemRequest;
use Illuminate\Http\Request;

class LichTrinhDiaDiemController extends Controller
{
    /**
     * Lấy danh sách địa điểm (kèm tọa độ) của một chuyến đi, sắp xếp theo thứ tự tham quan.
     */
    public function getDiaDiemByChuyenDi($id)
    {
        $lichTrinhs = LichTrinhDiaDiem::where('id_chuyen_di', $id)
            ->orderBy('thu_tu_tham_quan')
            ->get();

        $result = $lichTrinhs->map(function ($lt) {
            if ($lt->id_dia_diem) {
                $diaDiem = DiaDiem::find($lt->id_dia_diem);
                if (!$diaDiem) return null;
                return [
                    'id'               => $lt->id,
                    'type'             => 'dia_diem',
                    'thu_tu_tham_quan' => $lt->thu_tu_tham_quan,
                    'thu_tu'           => $lt->thu_tu_tham_quan,
                    'id_dia_diem'      => $lt->id_dia_diem,
                    'gio_bat_dau'      => $lt->gio_bat_dau,
                    'gio_ket_thuc'     => $lt->gio_ket_thuc,
                    'thoi_luong_phut'  => $lt->thoi_luong_phut,
                    'chi_phi_du_kien'  => $lt->chi_phi_du_kien,
                    'ghi_chu'          => $lt->ghi_chu,
                    'ten_dia_diem'     => $diaDiem->ten_dia_diem,
                    'dia_chi'          => $diaDiem->dia_chi,
                    'vi_do'            => (float) $diaDiem->vi_do,
                    'kinh_do'          => (float) $diaDiem->kinh_do,
                    'danh_gia'         => $diaDiem->danh_gia_trung_binh,
                    'gia_ve'           => $diaDiem->gia_ve,
                    'hinh_anh'         => $diaDiem->image,
                ];
            } else {
                // Free time
                return [
                    'id'               => $lt->id,
                    'type'             => 'free_time',
                    'thu_tu_tham_quan' => $lt->thu_tu_tham_quan,
                    'thu_tu'           => $lt->thu_tu_tham_quan,
                    'id_dia_diem'      => null,
                    'gio_bat_dau'      => $lt->gio_bat_dau,
                    'gio_ket_thuc'     => $lt->gio_ket_thuc,
                    'thoi_luong_phut'  => $lt->thoi_luong_phut,
                    'chi_phi_du_kien'  => $lt->chi_phi_du_kien,
                    'ghi_chu'          => $lt->ghi_chu,
                    'ten_dia_diem'     => 'Thời gian tự do',
                    'dia_chi'          => null,
                    'vi_do'            => null,
                    'kinh_do'          => null,
                    'danh_gia'         => null,
                    'gia_ve'           => null,
                    'hinh_anh'         => null,
                ];
            }
        })->filter()->values();

        return response()->json([
            'status' => 'success',
            'message' => 'Danh sách địa điểm theo chuyến đi.',
            'data' => $result,
        ], 200);
    }

    public function index()
    {
        $lichTrinhs = LichTrinhDiaDiem::all();
        return response()->json([
            'status' => 'success',
            'message' => 'Lấy danh sách lịch trình địa điểm thành công.', 
            'data' => $lichTrinhs
        ], 200);
    }

    public function store(StoreLichTrinhDiaDiemRequest $request)
    {
        $lichTrinh = LichTrinhDiaDiem::create([
            'id_chuyen_di'    => $request->id_chuyen_di,
            'id_dia_diem'     => $request->id_dia_diem,
            'thu_tu_tham_quan' => $request->thu_tu_tham_quan ?? 1,
            'gio_bat_dau'     => $request->gio_bat_dau,
            'gio_ket_thuc'    => $request->gio_ket_thuc,
            'thoi_luong_phut' => $request->thoi_luong_phut,
            'chi_phi_du_kien' => $request->chi_phi_du_kien,
        ]);
        return response()->json([
            'status' => 'success', 
            'message' => 'Tạo lịch trình địa điểm thành công.', 
            'data' => $lichTrinh
        ], 200);
    }

    public function show($id)
    {
        $lichTrinh = LichTrinhDiaDiem::find($id);
        if (!$lichTrinh) {
            return response()->json([
                'status' => 'error',
                 'message' => 'Không tìm thấy lịch trình địa điểm.'
                ], 404);
        }
        return response()->json([
            'status' => 'success', 
            'message' => 'Chi tiết lịch trình địa điểm.', 
            'data' => $lichTrinh
        ], 200);
    }

    public function update(UpdateLichTrinhDiaDiemRequest $request, $id)
    {
        $lichTrinh = LichTrinhDiaDiem::find($id);
        if (!$lichTrinh) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Không tìm thấy lịch trình địa điểm.'
            ], 404);
        }
        $lichTrinh->fill($request->all())->save();
        return response()->json([
            'status' => 'success', 
            'message' => 'Cập nhật lịch trình địa điểm thành công.', 
            'data' => $lichTrinh
        ], 200);
    }

    public function destroy($id)
    {
        $lichTrinh = LichTrinhDiaDiem::find($id);
        if (!$lichTrinh) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Không tìm thấy lịch trình địa điểm.'
            ], 404);
        }
        $lichTrinh->delete();
        return response()->json([
            'status' => 'success', 
            'message' => 'Xóa lịch trình địa điểm thành công.'
        ], 200);
    }

    public function swapDiaDiem($id)
    {
        // 1. Lấy chi tiết lịch trình hiện tại
        $lichTrinh = LichTrinhDiaDiem::find($id);
        if (!$lichTrinh || !$lichTrinh->id_dia_diem) {
            return response()->json(['status' => false, 'message' => 'Lịch trình không hợp lệ hoặc không có địa điểm.'], 404);
        }

        $chuyenDiId = $lichTrinh->id_chuyen_di;
        $diaDiemHienTai = DiaDiem::find($lichTrinh->id_dia_diem);
        if (!$diaDiemHienTai) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy địa điểm hiện tại.'], 404);
        }

        // 2. Lấy danh sách ID các địa điểm ĐÃ CÓ trong chuyến đi để tránh trùng lặp
        $diaDiemDaCo = LichTrinhDiaDiem::where('id_chuyen_di', $chuyenDiId)
            ->whereNotNull('id_dia_diem')
            ->pluck('id_dia_diem')->toArray();

        // 3. Tìm các địa điểm thay thế cùng danh mục, chưa có trong chuyến đi
        $candidates = DiaDiem::where('loai_dia_diem', $diaDiemHienTai->loai_dia_diem)
            ->whereNotIn('id', $diaDiemDaCo)
            ->get();

        if ($candidates->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy địa điểm phù hợp để thay thế.'
            ], 404);
        }

        // 4. Ưu tiên theo khoảng cách (nếu có tọa độ) rồi theo giá vé tương đương
        $lat1 = (float) $diaDiemHienTai->vi_do;
        $lng1 = (float) $diaDiemHienTai->kinh_do;
        $giaHienTai = (float) ($diaDiemHienTai->gia_ve ?? 0);

        $scored = $candidates->map(function ($d) use ($lat1, $lng1, $giaHienTai) {
            $lat2 = (float) $d->vi_do;
            $lng2 = (float) $d->kinh_do;

            // Haversine distance (km)
            $distScore = 9999;
            if ($lat1 && $lng1 && $lat2 && $lng2) {
                $R = 6371;
                $dLat = deg2rad($lat2 - $lat1);
                $dLng = deg2rad($lng2 - $lng1);
                $a = sin($dLat/2)**2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2)**2;
                $distScore = $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
            }

            // Giá vé: ưu tiên tương đương (chênh lệch càng nhỏ càng tốt)
            $gia = (float) ($d->gia_ve ?? 0);
            $giaDiff = abs($gia - $giaHienTai);

            // Score tổng hợp: distance (trọng số cao) + giá (trọng số thấp)
            $score = $distScore * 0.7 + ($giaDiff / max(1, $giaHienTai)) * 0.3;

            return ['place' => $d, 'score' => $score, 'distance_km' => round($distScore, 2)];
        })
        ->sortBy('score');

        // Lấy top 3 gần nhất, rồi random 1 cái để tránh luôn trả về cùng 1 địa điểm
        $top3 = $scored->take(3)->values();
        $chosen = $top3[rand(0, $top3->count() - 1)]['place'];

        // 5. Đổi địa điểm, giữ nguyên giờ bắt đầu – chỉ cập nhật thời lượng theo địa điểm mới
        $lichTrinh->id_dia_diem = $chosen->id;
        $newDuration = $chosen->thoi_gian_tham_quan_du_kien ?? $lichTrinh->thoi_luong_phut ?? 60;
        $lichTrinh->thoi_luong_phut = $newDuration;

        // Tính lại gio_ket_thuc từ gio_bat_dau + thoi_luong_phut mới
        if ($lichTrinh->gio_bat_dau) {
            $start = \Carbon\Carbon::createFromFormat('H:i', substr($lichTrinh->gio_bat_dau, 0, 5));
            $lichTrinh->gio_ket_thuc = $start->copy()->addMinutes($newDuration)->format('H:i');
        }

        $lichTrinh->save();

        // 6. Trả về đầy đủ dữ liệu địa điểm mới cho frontend đồng bộ
        return response()->json([
            'status'  => true,
            'message' => 'Thay đổi địa điểm thành công',
            'data'    => [
                'id'                         => $lichTrinh->id,
                'id_dia_diem'                => $chosen->id,
                'ten_dia_diem'               => $chosen->ten_dia_diem,
                'dia_chi'                    => $chosen->dia_chi,
                'vi_do'                      => (float) $chosen->vi_do,
                'kinh_do'                    => (float) $chosen->kinh_do,
                'gia_ve'                     => $chosen->gia_ve,
                'hinh_anh'                   => $chosen->image,
                'danh_gia_trung_binh'        => $chosen->danh_gia_trung_binh,
                'loai_dia_diem'              => $chosen->loai_dia_diem,
                'thoi_gian_tham_quan_du_kien' => $chosen->thoi_gian_tham_quan_du_kien,
                'gio_bat_dau'                => $lichTrinh->gio_bat_dau,
                'gio_ket_thuc'               => $lichTrinh->gio_ket_thuc,
                'thoi_luong_phut'            => $lichTrinh->thoi_luong_phut,
                'distance_km'                => $top3->first()['distance_km'] ?? null,
            ]
        ]);
    }

    public function reorder(Request $request)
    {
        $items = $request->input('items', []);
        $chuyenDiId = null;

        foreach ($items as $item) {
            $lichTrinh = LichTrinhDiaDiem::find($item['id']);
            if ($lichTrinh) {
                if (!$chuyenDiId) $chuyenDiId = $lichTrinh->id_chuyen_di;
                $lichTrinh->thu_tu_tham_quan = $item['thu_tu'];
                if (isset($item['gio_bat_dau'])) {
                    $lichTrinh->gio_bat_dau = $item['gio_bat_dau'];
                }
                if (isset($item['gio_ket_thuc'])) {
                    $lichTrinh->gio_ket_thuc = $item['gio_ket_thuc'];
                }
                $lichTrinh->save();
            }
        }

        // Broadcast realtime update
        if ($chuyenDiId) {
            $chuyenDi = \App\Models\ChuyenDi::find($chuyenDiId);
            if ($chuyenDi && $chuyenDi->id_nhom_du_lich) {
                broadcast(new \App\Events\ItineraryReordered(
                    $chuyenDi->id_nhom_du_lich,
                    $chuyenDiId,
                    ['message' => 'Manual reorder']
                ))->toOthers();
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật thứ tự thành công.'
        ]);
    }
}
