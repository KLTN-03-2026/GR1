<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DanhGia;
use App\Http\Requests\StoreDanhGiaRequest;
use App\Http\Requests\UpdateDanhGiaRequest;

class DanhGiaController extends Controller
{
    /**
     * Lấy danh sách tất cả đánh giá.
     */
    public function index()
    {
        $danhGias = DanhGia::with(['nguoiDung:id,ten,email', 'diaDiem:id,ten_dia_diem'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy danh sách đánh giá thành công.',
            'data'    => $danhGias,
        ], 200);
    }

    /**
     * Lấy danh sách đánh giá theo địa điểm.
     */
    public function getByPlace($id)
    {
        $danhGias = \App\Models\DanhGia::where('id_dia_diem', $id)
            ->where('trang_thai', 1) // Chỉ hiển thị những đánh giá đã được duyệt
            ->with('nguoiDung:id,ten,anh_dai_dien')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'status'  => 'success',
            'data'    => $danhGias,
        ]);
    }

    /**
     * Tạo mới một đánh giá.
     */
    public function store(StoreDanhGiaRequest $request)
    {
        $userId = auth('sanctum')->id();
        
        // If not logged in via sanctum, check the provided id (though auth is preferred)
        if (!$userId) {
            $userId = $request->id_nguoi_dung;
        }

        if (!$userId || $userId <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn cần đăng nhập để thực hiện chức năng này.'
            ], 401);
        }

        $danhGia = DanhGia::create([
            'id_nguoi_dung' => $userId,
            'id_dia_diem'   => $request->id_dia_diem,
            'so_sao'        => $request->so_sao,
            'noi_dung'      => $request->noi_dung,
            'trang_thai'    => 0, // Đánh giá của người dùng mặc định là chờ duyệt (0)
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Tạo đánh giá thành công.',
            'data'    => $danhGia,
        ], 200);
    }

    /**
     * Lấy chi tiết một đánh giá.
     */
    public function show($id)
    {
        $danhGia = DanhGia::find($id);

        if (!$danhGia) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Không tìm thấy đánh giá.',
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Chi tiết đánh giá.',
            'data'    => $danhGia,
        ], 200);
    }

    /**
     * Cập nhật một đánh giá.
     */
    public function update(UpdateDanhGiaRequest $request, $id)
    {
        $danhGia = DanhGia::find($id);

        if (!$danhGia) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Không tìm thấy đánh giá.',
            ], 404);
        }

        $danhGia->fill($request->all());
        $danhGia->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Cập nhật đánh giá thành công.',
            'data'    => $danhGia,
        ], 200);
    }

    /**
     * Xóa một đánh giá.
     */
    public function destroy($id)
    {
        $danhGia = DanhGia::find($id);

        if (!$danhGia) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Không tìm thấy đánh giá.',
            ], 404);
        }

        $danhGia->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Xóa đánh giá thành công.',
        ], 200);
    }
}
