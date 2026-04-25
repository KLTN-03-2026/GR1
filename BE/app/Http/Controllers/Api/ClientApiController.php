<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DiaDiem;
use App\Models\ChuyenDi;
use App\Models\LichTrinhDiaDiem;

class ClientApiController extends Controller
{
    /**
     * Lấy danh sách địa điểm cho client tạo lịch trình
     */
    public function getDiaDiem()
    {
        $diaDiems = DiaDiem::all()->map(function ($d) {
            return [
                'id' => $d->id,
                'ten_dia_diem' => $d->ten_dia_diem,
                'dia_chi' => $d->dia_chi,
                'hinh_anh' => $d->image, // Ánh xạ image sang hinh_anh cho frontend
                'gia_ve' => $d->gia_ve,
                'gio_mo_cua' => $d->gio_mo_cua,
                'gio_dong_cua' => $d->gio_dong_cua,
                'danh_gia_trung_binh' => $d->danh_gia_trung_binh,
                'id_danh_muc' => $d->loai_dia_diem, // Dùng loai_dia_diem làm danh mục
                'ten_danh_muc' => $d->loai_dia_diem,
                'mo_ta' => $d->mo_ta,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $diaDiems
        ], 200);
    }

    /**
     * Tạo chuyến đi mới cho user
     */
    public function createChuyenDi(Request $request)
    {
        // Validation
        $request->validate([
            'ten_chuyen_di' => 'required',
            'ngay_bat_dau'  => 'required|date|after_or_equal:today',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
        ], [
            'ten_chuyen_di.required' => 'Vui lòng nhập tên chuyến đi.',
            'ngay_bat_dau.required'  => 'Vui lòng chọn ngày bắt đầu.',
            'ngay_bat_dau.after_or_equal' => 'Ngày bắt đầu không được nhỏ hơn ngày hiện tại.',
            'ngay_ket_thuc.required' => 'Vui lòng chọn ngày kết thúc.',
            'ngay_ket_thuc.after'         => 'Ngày kết thúc phải lớn hơn ngày bắt đầu.',
        ]);

        // Tính số ngày
        $so_ngay = 1;
        if ($request->filled('ngay_bat_dau') && $request->filled('ngay_ket_thuc')) {
            $start = \Carbon\Carbon::parse($request->ngay_bat_dau);
            $end = \Carbon\Carbon::parse($request->ngay_ket_thuc);
            $so_ngay = max(1, $start->diffInDays($end) + 1);
        }

        $userId = auth('sanctum')->id();
        if (!$userId) {
            return response()->json(['status' => false, 'message' => 'Vui lòng đăng nhập để tạo chuyến đi.'], 401);
        }

        $chuyenDi = ChuyenDi::create([
            'id_nguoi_dung' => $userId,
            'ten_chuyen_di' => $request->ten_chuyen_di,
            'so_ngay'       => $so_ngay,
            'so_nguoi'      => $request->so_luong_thanh_vien ?? 1,
            'ngan_sach'     => $request->ngan_sach_du_kien ?? 0,
            'ngay_bat_dau'  => $request->ngay_bat_dau,
            'trang_thai'    => 1, // 1: Mới tạo
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Tạo chuyến đi thành công',
            'data' => $chuyenDi
        ], 200);
    }

    /**
     * Lấy danh sách chuyến đi của user (mới nhất xếp trước)
     */
    public function getChuyenDi(Request $request)
    {
        $userId = auth('sanctum')->id();
        if (!$userId) {
            return response()->json(['status' => false, 'message' => 'Vui lòng đăng nhập.'], 401);
        }

        $chuyenDis = ChuyenDi::where('id_nguoi_dung', $userId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $chuyenDis
        ], 200);
    }

    /**
     * Xóa chuyến đi
     */
    public function deleteChuyenDi(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:chuyen_dis,id'
        ]);

        $userId = auth('sanctum')->id();
        $chuyenDi = ChuyenDi::where('id', $request->id)
                            ->where('id_nguoi_dung', $userId)
                            ->first();

        if (!$chuyenDi) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy chuyến đi hoặc bạn không có quyền xóa'
            ], 404);
        }

        $chuyenDi->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa chuyến đi thành công'
        ], 200);
    }

    /**
     * Thêm nhiều địa điểm vào lịch trình chuyến đi
     */
    public function bulkCreateChiTiet(Request $request)
    {
        $id_chuyen_di = $request->id_chuyen_di;
        $items = $request->items ?? [];

        foreach ($items as $item) {
            LichTrinhDiaDiem::create([
                'id_chuyen_di'    => $id_chuyen_di,
                'id_dia_diem'     => is_numeric($item['id_dia_diem']) ? $item['id_dia_diem'] : null,
                'thu_tu_tham_quan' => $item['thu_tu_tham_quan'] ?? 1,
                'gio_bat_dau'     => $item['gio_bat_dau'] ?? null,
                'gio_ket_thuc'    => $item['gio_ket_thuc'] ?? null,
                'thoi_luong_phut' => $item['thoi_luong_phut'] ?? null,
                'ghi_chu'         => $item['ghi_chu'] ?? '',
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Lưu chi tiết lịch trình thành công'
        ], 200);
    }

    /**
     * Toggle yêu thích địa điểm
     */
    public function toggleFavorite(\Illuminate\Http\Request $request)
    {
        $id_dia_diem = $request->id_dia_diem;
        $id_nguoi_dung = auth()->id();

        if (!$id_nguoi_dung) {
            return response()->json(['status' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }

        $favorite = \App\Models\DiaDiemYeuThich::where('id_nguoi_dung', $id_nguoi_dung)
            ->where('id_dia_diem', $id_dia_diem)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['status' => true, 'is_favorite' => false]);
        } else {
            \App\Models\DiaDiemYeuThich::create([
                'id_nguoi_dung' => $id_nguoi_dung,
                'id_dia_diem' => $id_dia_diem
            ]);
            return response()->json(['status' => true, 'is_favorite' => true]);
        }
    }

    public function getFavorites()
    {
        $userId = auth()->id();
        $favorites = \App\Models\DiaDiem::whereHas('yeuThich', function($q) use ($userId) {
            $q->where('id_nguoi_dung', $userId);
        })->with('gallery')->get()->map(function($d) {
            $d->is_favorite = true;
            return $d;
        });

        return response()->json([
            'status' => 'success',
            'data' => $favorites
        ]);
    }
}
