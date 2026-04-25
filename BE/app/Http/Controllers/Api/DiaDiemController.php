<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiaDiem;
use App\Http\Requests\StoreDiaDiemRequest;
use App\Http\Requests\UpdateDiaDiemRequest;

class DiaDiemController extends Controller
{
    public function getAmThuc(\Illuminate\Http\Request $request)
    {
        // IDs: 1 to 6 are originally Ẩm thực, Quán ăn, Street food, Hải sản, Quán nhậu, Ăn vặt
        $loais = \App\Models\DanhMuc::whereIn('id', [1, 2, 3, 4, 5, 6])->pluck('ten_danh_muc')->toArray();
        $fallback = ['Quán ăn', 'Street food', 'Hải sản', 'Quán nhậu', 'Ăn vặt', 'Ẩm thực'];
        return $this->getDiaDiemByLoai($request, array_merge($loais, $fallback), 'Danh sách địa điểm ẩm thực.');
    }

    public function getCheckIn(\Illuminate\Http\Request $request)
    {
        // IDs: 7 to 13 are Check-in, Bãi biển, Ngắm cảnh, Lịch sử...
        $loais = \App\Models\DanhMuc::whereIn('id', [7, 8, 9, 10, 11, 12, 13])->pluck('ten_danh_muc')->toArray();
        $fallback = ['Cầu nổi tiếng','Bãi biển','Ngắm cảnh','Lịch sử','Phố cổ','Tự nhiên', 'Check-in', 'Điểm check-in'];
        return $this->getDiaDiemByLoai($request, array_merge($loais, $fallback), 'Danh sách địa điểm check-in.');
    }

    public function getGiaiTri(\Illuminate\Http\Request $request)
    {
        // IDs: 14 to 20 are Giải trí, Ngoài trời, Mua sắm, Xem phim...
        $loais = \App\Models\DanhMuc::whereIn('id', [14, 15, 16, 17, 18, 19, 20])->pluck('ten_danh_muc')->toArray();
        $fallback = ['Ngoài trời','Mua sắm','Xem phim','Công viên','Âm nhạc','Cafe', 'Giải trí', 'Khu vui chơi'];
        return $this->getDiaDiemByLoai($request, array_merge($loais, $fallback), 'Danh sách địa điểm giải trí.');
    }

    public function getTamLinh(\Illuminate\Http\Request $request)
    {
        // IDs: 21 to 26 are Tâm linh, Chùa, Đền, Nhà thờ, Thánh địa, Tu viện
        $loais = \App\Models\DanhMuc::whereIn('id', [21, 22, 23, 24, 25, 26])->pluck('ten_danh_muc')->toArray();
        $fallback = ['Chùa','Đền','Nhà thờ','Thánh địa','Tu viện', 'Tâm linh'];
        return $this->getDiaDiemByLoai($request, array_merge($loais, $fallback), 'Danh sách địa điểm tâm linh.');
    }

    private function getDiaDiemByLoai(\Illuminate\Http\Request $request, array $loais, string $message)
    {
        $userId = auth('sanctum')->id();
        
        $query = \App\Models\DiaDiem::with('gallery')->whereIn('loai_dia_diem', $loais);

        if ($request->filled('loai')) {
            $query->where('loai_dia_diem', $request->loai);
        }
        if ($request->filled('search')) {
            $kw = $request->search;
            $query->where(function ($q) use ($kw) {
                $q->where('ten_dia_diem', 'like', "%{$kw}%")
                  ->orWhere('mo_ta', 'like', "%{$kw}%");
            });
        }

        $data = $query->orderByDesc('danh_gia_trung_binh')->get()->map(function($d) use ($userId) {
            $d->is_favorite = $userId ? $d->yeuThich()->where('id_nguoi_dung', $userId)->exists() : false;
            return $d;
        });

        return response()->json(['status' => 'success', 'message' => $message, 'data' => $data], 200);
    }


    public function index()
    {
        $diaDiems = DiaDiem::all();

        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy danh sách địa điểm thành công.',
            'data'    => $diaDiems,
        ], 200);
    }

    /**
     * Tạo mới một địa điểm.
     */
    public function store(StoreDiaDiemRequest $request)
    {
        $data = $request->except('image');
        $diaDiem = DiaDiem::create($data);

        if ($request->has('image')) {
            \App\Models\HinhAnhDiaDiem::create([
                'id_dia_diem'   => $diaDiem->id,
                'duong_dan_anh' => $request->image,
                'is_main'       => true,
                'sort_order'    => 1
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Tạo địa điểm thành công.',
            'data'    => $diaDiem,
        ], 200);
    }

    /**
     * Lấy chi tiết một địa điểm.
     */
    public function show($id)
    {
        $diaDiem = DiaDiem::with('gallery')->find($id);

        if (!$diaDiem) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Không tìm thấy địa điểm.',
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Chi tiết địa điểm.',
            'data'    => $diaDiem,
        ], 200);
    }

    /**
     * Cập nhật thông tin một địa điểm.
     */
    public function update(UpdateDiaDiemRequest $request, $id)
    {
        $diaDiem = DiaDiem::find($id);

        if (!$diaDiem) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Không tìm thấy địa điểm.',
            ], 404);
        }

        $data = $request->except('image');
        $diaDiem->fill($data);
        $diaDiem->save();

        if ($request->has('image')) {
            // Update existing main image or create a new one
            \App\Models\HinhAnhDiaDiem::updateOrCreate(
                ['id_dia_diem' => $diaDiem->id, 'is_main' => true],
                ['duong_dan_anh' => $request->image, 'sort_order' => 1]
            );
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Cập nhật địa điểm thành công.',
            'data'    => $diaDiem,
        ], 200);
    }

    /**
     * Xóa một địa điểm.
     */
    public function destroy($id)
    {
        $diaDiem = DiaDiem::find($id);

        if (!$diaDiem) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Không tìm thấy địa điểm.',
            ], 404);
        }

        $diaDiem->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Xóa địa điểm thành công.',
        ], 200);
    }
}
