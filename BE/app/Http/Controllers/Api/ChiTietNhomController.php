<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChiTietNhom;
use App\Http\Requests\StoreChiTietNhomRequest;
use App\Http\Requests\UpdateChiTietNhomRequest;

class ChiTietNhomController extends Controller
{
    public function index()
    {
        $chiTietNhoms = ChiTietNhom::all();
        return response()->json([
            'status' => 'success', 
            'message' => 'Lấy danh sách chi tiết nhóm thành công.', 
            'data' => $chiTietNhoms
        ], 200);
    }

    public function store(StoreChiTietNhomRequest $request)
    {
        $chiTietNhom = ChiTietNhom::create([
            'id_nguoi_dung'   => $request->id_nguoi_dung,
            'id_nhom_du_lich' => $request->id_nhom_du_lich,
            'vai_tro'         => $request->vai_tro ?? 'thanh_vien',
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Thêm thành viên vào nhóm thành công.',
            'data' => $chiTietNhom
            ], 200);
    }

    public function show($id)
    {
        $chiTietNhom = ChiTietNhom::find($id);
        if (!$chiTietNhom) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Không tìm thấy chi tiết nhóm.'
            ], 404);
        }
        return response()->json([
            'status' => 'success', 
            'message' => 'Chi tiết nhóm.',
             'data' => $chiTietNhom
            ], 200);
    }

    public function update(UpdateChiTietNhomRequest $request, $id)
    {
        $chiTietNhom = ChiTietNhom::find($id);
        if (!$chiTietNhom) {
            return response()->json([
                'status' => 'error',
                 'message' => 'Không tìm thấy chi tiết nhóm.'
                ], 404);
        }
        $chiTietNhom->fill($request->all())->save();
        return response()->json([
            'status' => 'success', 'message' => 
            'Cập nhật chi tiết nhóm thành công.', 
            'data' => $chiTietNhom
        ], 200);
    }

    public function destroy($id)
    {
        $chiTietNhom = ChiTietNhom::find($id);
        if (!$chiTietNhom) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Không tìm thấy chi tiết nhóm.'
            ], 404);
        }
        $chiTietNhom->delete();
        return response()->json([
            'status' => 'success', 
            'message' => 'Xóa thành viên khỏi nhóm thành công.'
        ], 200);
    }
}
