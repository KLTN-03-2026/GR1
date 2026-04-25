<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LichTrinhDiaDiem;
use App\Models\DiaDiem;
use App\Http\Requests\StoreLichTrinhDiaDiemRequest;
use App\Http\Requests\UpdateLichTrinhDiaDiemRequest;

class LichTrinhDiaDiemController extends Controller
{
    /**
     * Lấy danh sách địa điểm (kèm tọa độ) của một chuyến đi, sắp xếp theo thứ tự tham quan.
     */
    public function getDiaDiemByChuyenDi($id)
    {
        $lichTrinhs = LichTrinhDiaDiem::where('id_chuyen_di', $id)
            ->orderBy('thu_tu_tham_quan')
            ->get();

        $result = $lichTrinhs->map(function ($lt) {
            if ($lt->id_dia_diem) {
                $diaDiem = DiaDiem::find($lt->id_dia_diem);
                if (!$diaDiem) return null;
                return [
                    'type'             => 'dia_diem',
                    'thu_tu_tham_quan' => $lt->thu_tu_tham_quan,
                    'thu_tu'           => $lt->thu_tu_tham_quan,
                    'id_dia_diem'      => $lt->id_dia_diem,
                    'gio_bat_dau'      => $lt->gio_bat_dau,
                    'gio_ket_thuc'     => $lt->gio_ket_thuc,
                    'thoi_luong_phut'  => $lt->thoi_luong_phut,
                    'chi_phi_du_kien'  => $lt->chi_phi_du_kien,
                    'ghi_chu'          => $lt->ghi_chu,
                    'ten_dia_diem'     => $diaDiem->ten_dia_diem,
                    'dia_chi'          => $diaDiem->dia_chi,
                    'vi_do'            => (float) $diaDiem->vi_do,
                    'kinh_do'          => (float) $diaDiem->kinh_do,
                    'danh_gia'         => $diaDiem->danh_gia_trung_binh,
                    'gia_ve'           => $diaDiem->gia_ve,
                    'hinh_anh'         => $diaDiem->image,
                ];
            } else {
                // Free time
                return [
                    'type'             => 'free_time',
                    'thu_tu_tham_quan' => $lt->thu_tu_tham_quan,
                    'thu_tu'           => $lt->thu_tu_tham_quan,
                    'id_dia_diem'      => null,
                    'gio_bat_dau'      => $lt->gio_bat_dau,
                    'gio_ket_thuc'     => $lt->gio_ket_thuc,
                    'thoi_luong_phut'  => $lt->thoi_luong_phut,
                    'chi_phi_du_kien'  => $lt->chi_phi_du_kien,
                    'ghi_chu'          => $lt->ghi_chu,
                    'ten_dia_diem'     => 'Thời gian tự do',
                    'dia_chi'          => null,
                    'vi_do'            => null,
                    'kinh_do'          => null,
                    'danh_gia'         => null,
                    'gia_ve'           => null,
                    'hinh_anh'         => null,
                ];
            }
        })->filter()->values();

        return response()->json([
            'status' => 'success',
            'message' => 'Danh sách địa điểm theo chuyến đi.',
            'data' => $result,
        ], 200);
    }

    public function index()
    {
        $lichTrinhs = LichTrinhDiaDiem::all();
        return response()->json([
            'status' => 'success',
            'message' => 'Lấy danh sách lịch trình địa điểm thành công.', 
            'data' => $lichTrinhs
        ], 200);
    }

    public function store(StoreLichTrinhDiaDiemRequest $request)
    {
        $lichTrinh = LichTrinhDiaDiem::create([
            'id_chuyen_di'    => $request->id_chuyen_di,
            'id_dia_diem'     => $request->id_dia_diem,
            'thu_tu_tham_quan' => $request->thu_tu_tham_quan ?? 1,
            'gio_bat_dau'     => $request->gio_bat_dau,
            'gio_ket_thuc'    => $request->gio_ket_thuc,
            'thoi_luong_phut' => $request->thoi_luong_phut,
            'chi_phi_du_kien' => $request->chi_phi_du_kien,
        ]);
        return response()->json([
            'status' => 'success', 
            'message' => 'Tạo lịch trình địa điểm thành công.', 
            'data' => $lichTrinh
        ], 200);
    }

    public function show($id)
    {
        $lichTrinh = LichTrinhDiaDiem::find($id);
        if (!$lichTrinh) {
            return response()->json([
                'status' => 'error',
                 'message' => 'Không tìm thấy lịch trình địa điểm.'
                ], 404);
        }
        return response()->json([
            'status' => 'success', 
            'message' => 'Chi tiết lịch trình địa điểm.', 
            'data' => $lichTrinh
        ], 200);
    }

    public function update(UpdateLichTrinhDiaDiemRequest $request, $id)
    {
        $lichTrinh = LichTrinhDiaDiem::find($id);
        if (!$lichTrinh) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Không tìm thấy lịch trình địa điểm.'
            ], 404);
        }
        $lichTrinh->fill($request->all())->save();
        return response()->json([
            'status' => 'success', 
            'message' => 'Cập nhật lịch trình địa điểm thành công.', 
            'data' => $lichTrinh
        ], 200);
    }

    public function destroy($id)
    {
        $lichTrinh = LichTrinhDiaDiem::find($id);
        if (!$lichTrinh) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Không tìm thấy lịch trình địa điểm.'
            ], 404);
        }
        $lichTrinh->delete();
        return response()->json([
            'status' => 'success', 
            'message' => 'Xóa lịch trình địa điểm thành công.'
        ], 200);
    }
}
