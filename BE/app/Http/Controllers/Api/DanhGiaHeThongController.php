<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DanhGiaHeThong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DanhGiaHeThongController extends Controller
{
    /**
     * POST /api/client/danh-gia-he-thong
     * Người dùng gửi đánh giá mức độ hài lòng sau khi tạo lịch trình
     */
    public function store(Request $request)
    {
        // Bắt buộc đăng nhập (do route này có thể chưa áp dụng middleware auth:sanctum)
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Vui lòng đăng nhập để gửi đánh giá.',
            ], 401);
        }

        $request->validate([
            'muc_do_hai_long' => 'required|integer|min:1|max:5',
            'noi_dung'        => 'nullable|string|max:2000',
        ], [
            'muc_do_hai_long.required' => 'Vui lòng chọn mức độ hài lòng.',
            'muc_do_hai_long.min'      => 'Mức độ không hợp lệ.',
            'muc_do_hai_long.max'      => 'Mức độ không hợp lệ.',
        ]);

        $danhGia = DanhGiaHeThong::create([
            'muc_do_hai_long' => $request->muc_do_hai_long,
            'noi_dung'        => $request->noi_dung,
            'ip_address'      => $request->ip(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Cảm ơn bạn đã đánh giá!',
            'data'    => $danhGia,
        ], 201);
    }

    /**
     * GET /api/admin/danh-gia-he-thong
     * Lấy toàn bộ danh sách + thống kê tổng hợp
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 20);
        $rating  = $request->get('rating');   // filter by mức độ (1-5)
        $search  = $request->get('search');   // tìm theo nội dung

        $query = DanhGiaHeThong::orderByDesc('created_at');

        if ($rating) {
            $query->where('muc_do_hai_long', $rating);
        }

        if ($search) {
            $query->where('noi_dung', 'like', "%{$search}%");
        }

        $list = $query->paginate($perPage);

        // ── Thống kê tổng hợp ──────────────────────────────────────
        $stats = DB::table('danh_gia_he_thong')
            ->select(
                DB::raw('COUNT(*) as tong_so'),
                DB::raw('ROUND(AVG(muc_do_hai_long), 2) as diem_trung_binh'),
                DB::raw('SUM(CASE WHEN muc_do_hai_long = 1 THEN 1 ELSE 0 END) as rat_te'),
                DB::raw('SUM(CASE WHEN muc_do_hai_long = 2 THEN 1 ELSE 0 END) as te'),
                DB::raw('SUM(CASE WHEN muc_do_hai_long = 3 THEN 1 ELSE 0 END) as binh_thuong'),
                DB::raw('SUM(CASE WHEN muc_do_hai_long = 4 THEN 1 ELSE 0 END) as tot'),
                DB::raw('SUM(CASE WHEN muc_do_hai_long = 5 THEN 1 ELSE 0 END) as rat_tot')
            )
            ->first();

        // Phân bố theo ngày (30 ngày gần nhất)
        $trend = DB::table('danh_gia_he_thong')
            ->select(
                DB::raw('DATE(created_at) as ngay'),
                DB::raw('COUNT(*) as so_luong'),
                DB::raw('ROUND(AVG(muc_do_hai_long), 2) as diem_tb')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('ngay')
            ->orderBy('ngay')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $list,
            'stats'  => $stats,
            'trend'  => $trend,
        ]);
    }

    /**
     * DELETE /api/admin/danh-gia-he-thong/{id}
     */
    public function destroy($id)
    {
        $item = DanhGiaHeThong::find($id);
        if (! $item) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy.'], 404);
        }
        $item->delete();
        return response()->json(['status' => true, 'message' => 'Đã xoá đánh giá.']);
    }
}
