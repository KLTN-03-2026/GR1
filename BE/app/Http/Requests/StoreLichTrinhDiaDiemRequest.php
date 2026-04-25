<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLichTrinhDiaDiemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_chuyen_di'    => 'required|integer|exists:chuyen_dis,id',
            'id_dia_diem'     => 'required|integer|exists:dia_diems,id',
            'thu_tu_tham_quan' => 'nullable|integer|min:1',
            'gio_bat_dau'     => 'nullable|string|max:5',
            'gio_ket_thuc'    => 'nullable|string|max:5',
            'thoi_luong_phut' => 'nullable|integer|min:0',
            'chi_phi_du_kien' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'id_chuyen_di.required'     => 'ID chuyến đi là bắt buộc.',
            'id_chuyen_di.integer'      => 'ID chuyến đi phải là số nguyên.',
            'id_chuyen_di.exists'       => 'Chuyến đi không tồn tại.',
            'id_dia_diem.required'      => 'ID địa điểm là bắt buộc.',
            'id_dia_diem.integer'       => 'ID địa điểm phải là số nguyên.',
            'id_dia_diem.exists'        => 'Địa điểm không tồn tại.',
            'thu_tu_tham_quan.integer'  => 'Thứ tự tham quan phải là số nguyên.',
            'thu_tu_tham_quan.min'      => 'Thứ tự tham quan phải ít nhất là 1.',
            'gio_bat_dau.string'        => 'Giờ bắt đầu phải là chuỗi ký tự (HH:MM).',
            'gio_ket_thuc.string'       => 'Giờ kết thúc phải là chuỗi ký tự (HH:MM).',
            'thoi_luong_phut.integer'   => 'Thời lượng phải là số nguyên (phút).',
            'thoi_luong_phut.min'       => 'Thời lượng không được âm.',
            'chi_phi_du_kien.numeric'   => 'Chi phí dự kiến phải là số.',
            'chi_phi_du_kien.min'       => 'Chi phí dự kiến không được nhỏ hơn 0.',
        ];
    }
}
