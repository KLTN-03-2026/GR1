<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChuyenDi;
use App\Http\Requests\StoreChuyenDiRequest;
use App\Http\Requests\UpdateChuyenDiRequest;

class ChuyenDiController extends Controller
{
    public function index()
    {
        $chuyenDis = ChuyenDi::all();
        return response()->json([
            'status' => 'success', 
            'message' => 'Lấy danh sách chuyến đi thành công.', 
            'data' => $chuyenDis], 200);
    }

    public function store(StoreChuyenDiRequest $request)
    {
        $chuyenDi = ChuyenDi::create([
            'id_nguoi_dung'   => $request->id_nguoi_dung,
            'id_nhom_du_lich' => $request->id_nhom_du_lich,
            'ten_chuyen_di'   => $request->ten_chuyen_di,
            'so_ngay'         => $request->so_ngay ?? 1,
            'so_nguoi'        => $request->so_nguoi ?? 1,
            'ngan_sach'       => $request->ngan_sach,
            'ngay_bat_dau'    => $request->ngay_bat_dau,
            'trang_thai'      => $request->trang_thai ?? 1,
        ]);
        return response()->json([
            'status' => 'success', 
            'message' => 'Tạo chuyến đi thành công.', 
            'data' => $chuyenDi], 200);
    }

    public function show($id)
    {
        $chuyenDi = ChuyenDi::find($id);
        if (!$chuyenDi) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Không tìm thấy chuyến đi.'
            ], 404);
        }

        $userId = auth('sanctum')->id();
        $is_leader = false;
        if ($chuyenDi->id_nhom_du_lich && $userId) {
            $chiTietNhom = \App\Models\ChiTietNhom::where('id_nguoi_dung', $userId)
                ->where('id_nhom_du_lich', $chuyenDi->id_nhom_du_lich)
                ->first();
            if ($chiTietNhom && $chiTietNhom->vai_tro === 'truong_nhom') {
                $is_leader = true;
            }
        }

        $chuyenDi->is_leader = $is_leader;

        return response()->json([
            'status' => 'success',
             'message' => 'Chi tiết chuyến đi.', 
             'data' => $chuyenDi
            ], 200);
    }

    public function update(UpdateChuyenDiRequest $request, $id)
    {
        $chuyenDi = ChuyenDi::find($id);
        if (!$chuyenDi) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Không tìm thấy chuyến đi.'
            ], 404);
        }
        $chuyenDi->fill($request->all())->save();
        return response()->json([
            'status' => 'success', 
            'message' => 'Cập nhật chuyến đi thành công.', 
            'data' => $chuyenDi
        ], 200);
    }

    public function destroy($id)
    {
        $chuyenDi = ChuyenDi::find($id);
        if (!$chuyenDi) {
            return response()->json([
                'status' => 'error',
                 'message' => 'Không tìm thấy chuyến đi.'
                ], 404);
        }
        $chuyenDi->delete();
        return response()->json([
            'status' => 'success',
             'message' => 'Xóa chuyến đi thành công.'
            ], 200);
    }
}
