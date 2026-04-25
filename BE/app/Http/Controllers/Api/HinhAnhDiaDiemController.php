<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HinhAnhDiaDiem;
use App\Http\Requests\StoreHinhAnhDiaDiemRequest;
use App\Http\Requests\UpdateHinhAnhDiaDiemRequest;

class HinhAnhDiaDiemController extends Controller
{
    /**
     * Lấy danh sách tất cả hình ảnh địa điểm.
     */
    public function index()
    {
        $hinhAnhs = HinhAnhDiaDiem::all();

        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy danh sách hình ảnh địa điểm thành công.',
            'data'    => $hinhAnhs,
        ], 200);
    }

    /**
     * Tạo mới một hình ảnh địa điểm.
     */
    public function store(StoreHinhAnhDiaDiemRequest $request)
    {
        $data = $request->only([
            'id_dia_diem',
            'duong_dan_anh',
            'is_main',
            'sort_order',
        ]);

        $hinhAnh = HinhAnhDiaDiem::create($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Tạo hình ảnh địa điểm thành công.',
            'data'    => $hinhAnh,
        ], 200);
    }

    /**
     * Lấy chi tiết một hình ảnh địa điểm.
     */
    public function show($id)
    {
        $hinhAnh = HinhAnhDiaDiem::find($id);

        if (!$hinhAnh) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Không tìm thấy hình ảnh địa điểm.',
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Chi tiết hình ảnh địa điểm.',
            'data'    => $hinhAnh,
        ], 200);
    }

    /**
     * Cập nhật thông tin một hình ảnh địa điểm.
     */
    public function update(UpdateHinhAnhDiaDiemRequest $request, $id)
    {
        $hinhAnh = HinhAnhDiaDiem::find($id);

        if (!$hinhAnh) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Không tìm thấy hình ảnh địa điểm.',
            ], 404);
        }

        $hinhAnh->fill($request->only([
            'id_dia_diem',
            'duong_dan_anh',
            'is_main',
            'sort_order',
        ]));
        $hinhAnh->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Cập nhật hình ảnh địa điểm thành công.',
            'data'    => $hinhAnh,
        ], 200);
    }

    /**
     * Xóa một hình ảnh địa điểm.
     */
    public function destroy($id)
    {
        $hinhAnh = HinhAnhDiaDiem::find($id);

        if (!$hinhAnh) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Không tìm thấy hình ảnh địa điểm.',
            ], 404);
        }

        $hinhAnh->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Xóa hình ảnh địa điểm thành công.',
        ], 200);
    }

    public function getByDiaDiem($id_dia_diem)
    {
        $hinhAnhs = HinhAnhDiaDiem::where('id_dia_diem', $id_dia_diem)->orderByDesc('is_main')->get();
        return response()->json([
            'status' => 'success',
            'data' => $hinhAnhs
        ]);
    }

    public function setMainImage($id)
    {
        $hinhAnh = HinhAnhDiaDiem::find($id);
        if (!$hinhAnh) {
            return response()->json(['status'  => 'error', 'message' => 'Không tìm thấy hình ảnh.'], 404);
        }

        HinhAnhDiaDiem::where('id_dia_diem', $hinhAnh->id_dia_diem)->update(['is_main' => false]);
        $hinhAnh->is_main = true;
        $hinhAnh->save();

        return response()->json(['status' => 'success', 'message' => 'Đã đặt làm ảnh chính.', 'data' => $hinhAnh], 200);
    }
}
