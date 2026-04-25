<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChucVu;
use Illuminate\Support\Str;
use App\Http\Requests\StoreChucVuRequest;
use App\Http\Requests\UpdateChucVuRequest;

class ChucVuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chucVus = ChucVu::with('chucNangs')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy danh sách chức vụ thành công.',
            'data' => $chucVus
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChucVuRequest $request)
    {
        $chucVu = ChucVu::create([
            'ten_chuc_vu' => $request->ten_chuc_vu,
            'slug_chuc_vu' => Str::slug($request->ten_chuc_vu),
            'mo_ta' => $request->mo_ta,
            'tinh_trang' => $request->tinh_trang ?? 1,
        ]);

        if ($request->has('permissions')) {
            $permissionIds = \App\Models\ChucNang::whereIn('ma_chuc_nang', $request->permissions)->pluck('id');
            $chucVu->chucNangs()->sync($permissionIds);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Tạo chức vụ thành công.',
            'data' => $chucVu->load('chucNangs')
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $chucVu = ChucVu::with('chucNangs')->find($id);

        if (!$chucVu) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy chức vụ.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Chi tiết chức vụ.',
            'data' => $chucVu
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChucVuRequest $request, $id)
    {
        $chucVu = ChucVu::find($id);

        if (!$chucVu) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy chức vụ.'
            ], 404);
        }

        if ($request->has('ten_chuc_vu')) {
            $chucVu->ten_chuc_vu = $request->ten_chuc_vu;
            $chucVu->slug_chuc_vu = Str::slug($request->ten_chuc_vu);
        }

        if ($request->has('mo_ta')) {
            $chucVu->mo_ta = $request->mo_ta;
        }

        if ($request->has('tinh_trang')) {
            $chucVu->tinh_trang = $request->tinh_trang;
        }
        
        $chucVu->save();

        if ($request->has('permissions')) {
            // Giả sử request trả lên array các mã permission ['user_view', 'place_edit']
            $permissionIds = \App\Models\ChucNang::whereIn('ma_chuc_nang', $request->permissions)->pluck('id');
            $chucVu->chucNangs()->sync($permissionIds);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật chức vụ thành công.',
            'data' => $chucVu->load('chucNangs')
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $chucVu = ChucVu::find($id);

        if (!$chucVu) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy chức vụ.'
            ], 404);
        }

        if ($chucVu->id === 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không thể xóa chức vụ Quản trị viên hệ thống.'
            ], 403);
        }

        $chucVu->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Xóa chức vụ thành công.'
        ], 200);
    }
}
