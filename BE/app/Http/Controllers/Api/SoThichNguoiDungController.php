<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SoThichNguoiDung;
use App\Http\Requests\StoreSoThichNguoiDungRequest;
use App\Http\Requests\UpdateSoThichNguoiDungRequest;

class SoThichNguoiDungController extends Controller
{
    public function index()
    {
        $soThichs = SoThichNguoiDung::all();
        return response()->json([
            'status' => 'success', 
            'message' => 'Lấy danh sách sở thích người dùng thành công.', 
            'data' => $soThichs
        ], 200);
    }

    public function store(StoreSoThichNguoiDungRequest $request)
    {
        $soThich = SoThichNguoiDung::create([
            'id_nguoi_dung'    => $request->id_nguoi_dung,
            'id_danh_muc'      => $request->id_danh_muc,
            'muc_do_yeu_thich' => $request->muc_do_yeu_thich ?? 1,
        ]);
        return response()->json([
            'status' => 'success', 
            'message' => 'Tạo sở thích người dùng thành công.', 
            'data' => $soThich
        ], 200);
    }

    public function show($id)
    {
        $soThich = SoThichNguoiDung::find($id);
        if (!$soThich) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Không tìm thấy sở thích người dùng.'
            ], 404);
        }
        return response()->json([
            'status' => 'success', 
            'message' => 'Chi tiết sở thích người dùng.',
             'data' => $soThich
            ], 200);
    }

    public function update(UpdateSoThichNguoiDungRequest $request, $id)
    {
        $soThich = SoThichNguoiDung::find($id);
        if (!$soThich) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Không tìm thấy sở thích người dùng.'
            ], 404);
        }
        $soThich->fill($request->all())->save();
        return response()->json([
            'status' => 'success', 
            'message' => 'Cập nhật sở thích người dùng thành công.',
             'data' => $soThich
            ], 200);
    }

    public function destroy($id)
    {
        $soThich = SoThichNguoiDung::find($id);
        if (!$soThich) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Không tìm thấy sở thích người dùng.'
            ], 404);
        }
        $soThich->delete();
        return response()->json(['status' => 'success',
         'message' => 'Xóa sở thích người dùng thành công.'
        ], 200);
    }
}
