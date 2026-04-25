<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DanhMucController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $danhMucs = DanhMuc::select('danh_mucs.*')
            ->selectRaw('(SELECT COUNT(*) FROM chi_tiet_danh_mucs WHERE chi_tiet_danh_mucs.id_danh_muc = danh_mucs.id) as placesCount')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy danh sách danh mục thành công.',
            'data' => $danhMucs
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_danh_muc' => 'required|string|max:255',
        ]);

        $danhMuc = DanhMuc::create([
            'ten_danh_muc' => $request->ten_danh_muc,
            'mo_ta' => $request->mo_ta ?? '',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Tạo danh mục thành công.',
            'data' => $danhMuc
        ], 200);
    }

    public function show($id)
    {
        $danhMuc = DanhMuc::find($id);

        if (!$danhMuc) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy danh mục.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Chi tiết danh mục.',
            'data' => $danhMuc
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $danhMuc = DanhMuc::find($id);

        if (!$danhMuc) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy danh mục.'
            ], 404);
        }

        $oldName = $danhMuc->ten_danh_muc;

        $danhMuc->fill($request->all());
        $danhMuc->save();

        if ($oldName !== $danhMuc->ten_danh_muc) {
            // Update all related places so their badges automatically reflect new category name
            DB::table('dia_diems')
                ->where('loai_dia_diem', $oldName)
                ->update(['loai_dia_diem' => $danhMuc->ten_danh_muc]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật danh mục thành công.',
            'data' => $danhMuc
        ], 200);
    }

    public function destroy($id)
    {
        $danhMuc = DanhMuc::find($id);

        if (!$danhMuc) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy danh mục.'
            ], 404);
        }

        // Clean up linked dependencies
        DB::table('chi_tiet_danh_mucs')->where('id_danh_muc', $id)->delete();
        
        // Reset places using this category back to default string 
        DB::table('dia_diems')->where('loai_dia_diem', $danhMuc->ten_danh_muc)->update(['loai_dia_diem' => 'Khác']);

        $danhMuc->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Xóa danh mục thành công.'
        ], 200);
    }
}
