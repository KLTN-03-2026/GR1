<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChucNang;
use App\Http\Requests\StoreChucNangRequest;
use App\Http\Requests\UpdateChucNangRequest;

class ChucNangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Group them by nhom_chuc_nang directly or let frontend do it
        $chucNangs = ChucNang::all();

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy danh sách chức năng thành công.',
            'data' => $chucNangs
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChucNangRequest $request)
    {
        $chucNang = ChucNang::create([
            'ten_chuc_nang' => $request->ten_chuc_nang,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Tạo chức năng thành công.',
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $chucNang = ChucNang::find($id);

        if (!$chucNang) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy chức năng.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Chi tiết chức năng.',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChucNangRequest $request, $id)
    {
        $chucNang = ChucNang::find($id);

        if (!$chucNang) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy chức năng.'
            ], 404);
        }

        $chucNang->fill($request->all());
        $chucNang->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật chức năng thành công.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $chucNang = ChucNang::find($id);

        if (!$chucNang) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy chức năng.'
            ], 404);
        }

        $chucNang->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Xóa chức năng thành công.'
        ], 200);
    }
}
