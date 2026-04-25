<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChiPhiPhatSinh;
use App\Http\Requests\StoreChiPhiPhatSinhRequest;
use App\Http\Requests\UpdateChiPhiPhatSinhRequest;
use Illuminate\Http\Request;

class ChiPhiPhatSinhController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chiPhis = ChiPhiPhatSinh::with('nguoiTra:id,ten,anh_dai_dien')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy danh sách chi phí phát sinh thành công.',
            'data' => $chiPhis
        ]);
    }

    public function getByChuyenDi($id)
    {
        $chiPhis = ChiPhiPhatSinh::where('id_chuyen_di', $id)->with('nguoiTra:id,ten,anh_dai_dien')->orderByDesc('ngay_chi')->get();
        return response()->json([
            'status' => 'success',
            'data' => $chiPhis
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_chuyen_di' => 'required|integer',
            'noi_dung'     => 'required|string|max:255',
            'tong_chi_phi' => 'required|numeric|min:0',
            'ngay_chi'     => 'nullable|date',
            'loai_chi_phi' => 'nullable|string|max:50',
        ]);

        // Tự động gán người trả từ token đăng nhập nếu không truyền vào
        $validated['id_nguoi_tra'] = $request->input('id_nguoi_tra') ?? auth('sanctum')->id();

        $chiPhi = ChiPhiPhatSinh::create($validated);
        $chiPhi->load('nguoiTra:id,ten,anh_dai_dien');

        return response()->json([
            'status' => 'success',
            'message' => 'Tạo chi phí phát sinh thành công.',
            'data' => $chiPhi
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $chiPhi = ChiPhiPhatSinh::find($id);

        if (!$chiPhi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy chi phí phát sinh.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Chi tiết chi phí phát sinh.',
            'data' => $chiPhi->load('nguoiTra:id,ten,anh_dai_dien')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChiPhiPhatSinhRequest $request, $id)
    {
        $chiPhi = ChiPhiPhatSinh::find($id);

        if (!$chiPhi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy chi phí phát sinh.'
            ], 404);
        }

        $data = $request->only(['id_chuyen_di', 'noi_dung', 'tong_chi_phi', 'id_nguoi_tra', 'ngay_chi', 'loai_chi_phi']);
        $chiPhi->fill($data);
        $chiPhi->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật chi phí phát sinh thành công.',
            'data' => $chiPhi
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $chiPhi = ChiPhiPhatSinh::find($id);

        if (!$chiPhi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy chi phí phát sinh.'
            ], 404);
        }

        $chiPhi->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Xóa chi phí phát sinh thành công.'
        ]);
    }
}
