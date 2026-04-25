<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChiTietDanhMuc;
use App\Http\Requests\StoreChiTietDanhMucRequest;
use App\Http\Requests\UpdateChiTietDanhMucRequest;
use Illuminate\Http\Request;

class ChiTietDanhMucController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chiTietDanhMucs = ChiTietDanhMuc::all();

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy danh sách chi tiết danh mục thành công.'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChiTietDanhMucRequest $request)
    {
        // Kiểm tra xem địa điểm này đã có danh mục này chưa
        $exists = ChiTietDanhMuc::where('id_danh_muc', $request->id_danh_muc)
                                ->where('id_dia_diem', $request->id_dia_diem)
                                ->first();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Địa điểm này đã được gắn danh mục này.',
            ], 400);
        }

        $chiTietDanhMuc = ChiTietDanhMuc::create([
            'id_danh_muc' => $request->id_danh_muc,
            'id_dia_diem' => $request->id_dia_diem,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Gán danh mục cho địa điểm thành công.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $chiTietDanhMuc = ChiTietDanhMuc::find($id);

        if (!$chiTietDanhMuc) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy chi tiết danh mục.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy chi tiết danh mục thành công.'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChiTietDanhMucRequest $request, $id)
    {
        $chiTietDanhMuc = ChiTietDanhMuc::find($id);

        if (!$chiTietDanhMuc) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy chi tiết danh mục.'
            ], 404);
        }

        $chiTietDanhMuc->fill($request->all());
        $chiTietDanhMuc->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật chi tiết danh mục thành công.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $chiTietDanhMuc = ChiTietDanhMuc::find($id);

        if (!$chiTietDanhMuc) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy chi tiết danh mục.'
            ], 404);
        }

        $chiTietDanhMuc->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Xóa chi tiết danh mục thành công.'
        ]);
    }
}
