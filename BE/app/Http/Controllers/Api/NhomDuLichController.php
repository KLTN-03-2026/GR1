<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NhomDuLich;
use App\Http\Requests\StoreNhomDuLichRequest;
use App\Http\Requests\UpdateNhomDuLichRequest;

class NhomDuLichController extends Controller
{
    public function index()
    {
        $nhomDuLichs = NhomDuLich::all();
        return response()->json([
            'status' => 'success',
             'message' => 'Lấy danh sách nhóm du lịch thành công.', 
             'data' => $nhomDuLichs
            ], 200);
    }

    public function store(StoreNhomDuLichRequest $request)
    {
        $nhomDuLich = NhomDuLich::create([
            'id_tao_nhom' => $request->id_tao_nhom,
            'ten_nhom'    => $request->ten_nhom,
            'nguoi_tao'   => $request->nguoi_tao,
        ]);
        return response()->json(['status' => 'success'
        , 'message' => 'Tạo nhóm du lịch thành công.',
         'data' => $nhomDuLich
        ], 200);
    }

    public function show($id)
    {
        $nhomDuLich = NhomDuLich::find($id);
        if (!$nhomDuLich) {
            return response()->json([
                'status' => 'error',
                 'message' => 'Không tìm thấy nhóm du lịch.'
                ], 404);
        }
        return response()->json(['status' => 'success', 
        'message' => 'Chi tiết nhóm du lịch.', 
        'data' => $nhomDuLich
        ], 200);
    }

    public function update(UpdateNhomDuLichRequest $request, $id)
    {
        $nhomDuLich = NhomDuLich::find($id);
        if (!$nhomDuLich) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Không tìm thấy nhóm du lịch.'
            ], 404);
        }
        $nhomDuLich->fill($request->all())->save();
        return response()->json([
            'status' => 'success',
             'message' => 'Cập nhật nhóm du lịch thành công.', 
             'data' => $nhomDuLich
            ], 200);
    }

    public function destroy($id)
    {
        $nhomDuLich = NhomDuLich::find($id);
        if (!$nhomDuLich) {
            return response()->json([
                'status' => 'error',
                 'message' => 'Không tìm thấy nhóm du lịch.']
                 , 404);
        }
        $nhomDuLich->delete();
        return response()->json([
            'status' => 'success', 
            'message' => 'Xóa nhóm du lịch thành công.'
        ], 200);
    }
}
