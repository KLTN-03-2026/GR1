<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChiTietNhomRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_nguoi_dung'   => 'required|integer|exists:nguoi_dungs,id',
            'id_nhom_du_lich' => 'required|integer|exists:nhom_du_lichs,id',
            'vai_tro'         => 'nullable|string|in:truong_nhom,thanh_vien',
        ];
    }

    public function messages(): array
    {
        return [
            'id_nguoi_dung.required'   => 'ID người dùng là bắt buộc.',
            'id_nguoi_dung.integer'    => 'ID người dùng phải là số nguyên.',
            'id_nguoi_dung.exists'     => 'Người dùng không tồn tại.',
            'id_nhom_du_lich.required' => 'ID nhóm du lịch là bắt buộc.',
            'id_nhom_du_lich.integer'  => 'ID nhóm du lịch phải là số nguyên.',
            'id_nhom_du_lich.exists'   => 'Nhóm du lịch không tồn tại.',
            'vai_tro.in'               => 'Vai trò không hợp lệ (truong_nhom, thanh_vien).',
        ];
    }
}
