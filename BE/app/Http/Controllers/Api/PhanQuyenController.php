<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhanQuyen;
use App\Http\Requests\StorePhanQuyenRequest;
use App\Http\Requests\UpdatePhanQuyenRequest;

class PhanQuyenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $phanQuyens = PhanQuyen::all();

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy danh sách phân quyền thành công.',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePhanQuyenRequest $request)
    {
        // Kiểm tra xem đã tồn tại chưa để tránh trùng lặp
        $exists = PhanQuyen::where('id_chuc_vu', $request->id_chuc_vu)
                           ->where('id_chuc_nang', $request->id_chuc_nang)
                           ->first();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quyền này đã được gán cho chức vụ tương ứng.',
            ], 400);
        }

        $phanQuyen = PhanQuyen::create([
            'id_chuc_vu' => $request->id_chuc_vu,
            'id_chuc_nang' => $request->id_chuc_nang,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Tính năng phân quyền được tạo thành công.',
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $phanQuyen = PhanQuyen::find($id);

        if (!$phanQuyen) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy phân quyền này.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Chi tiết phân quyền.',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePhanQuyenRequest $request, $id)
    {
        $phanQuyen = PhanQuyen::find($id);

        if (!$phanQuyen) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy phân quyền này.'
            ], 404);
        }

        $phanQuyen->fill($request->all());
        $phanQuyen->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật phân quyền thành công.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $phanQuyen = PhanQuyen::find($id);

        if (!$phanQuyen) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy phân quyền này.'
            ], 404);
        }

        $phanQuyen->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Xóa phân quyền thành công.'
        ], 200);
    }
}
