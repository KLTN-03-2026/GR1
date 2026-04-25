<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChuyenDiRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_nguoi_dung'   => 'sometimes|required|integer|exists:nguoi_dungs,id',
            'id_nhom_du_lich' => 'nullable|integer|exists:nhom_du_lichs,id',
            'ten_chuyen_di'   => 'sometimes|required|string|max:255',
            'so_ngay'         => 'nullable|integer|min:1',
            'so_nguoi'        => 'nullable|integer|min:1',
            'ngan_sach'       => 'nullable|numeric|min:0',
            'ngay_bat_dau'    => 'nullable|date',
            'trang_thai'      => 'nullable|integer|in:0,1,2,3',
        ];
    }

    public function messages(): array
    {
        return [
            'id_nguoi_dung.required'  => 'ID người dùng là bắt buộc.',
            'id_nguoi_dung.integer'   => 'ID người dùng phải là số nguyên.',
            'id_nguoi_dung.exists'    => 'Người dùng không tồn tại.',
            'id_nhom_du_lich.integer' => 'ID nhóm du lịch phải là số nguyên.',
            'id_nhom_du_lich.exists'  => 'Nhóm du lịch không tồn tại.',
            'ten_chuyen_di.required'  => 'Tên chuyến đi là bắt buộc.',
            'ten_chuyen_di.string'    => 'Tên chuyến đi phải là chuỗi ký tự.',
            'ten_chuyen_di.max'       => 'Tên chuyến đi không được vượt quá 255 ký tự.',
            'so_ngay.integer'         => 'Số ngày phải là số nguyên.',
            'so_ngay.min'             => 'Số ngày phải ít nhất là 1.',
            'so_nguoi.integer'        => 'Số người phải là số nguyên.',
            'so_nguoi.min'            => 'Số người phải ít nhất là 1.',
            'ngan_sach.numeric'       => 'Ngân sách phải là số.',
            'ngan_sach.min'           => 'Ngân sách không được nhỏ hơn 0.',
            'ngay_bat_dau.date'       => 'Ngày bắt đầu không đúng định dạng.',
            'trang_thai.integer'      => 'Trạng thái phải là số nguyên.',
            'trang_thai.in'           => 'Trạng thái không hợp lệ (0: Hủy, 1: Lên kế hoạch, 2: Đang đi, 3: Hoàn thành).',
        ];
    }
}
