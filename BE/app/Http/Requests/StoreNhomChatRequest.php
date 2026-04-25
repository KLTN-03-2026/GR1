<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNhomChatRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_nhom_du_lich'  => 'required|integer|exists:nhom_du_lichs,id',
            'id_chi_tiet_nhom' => 'required|integer|exists:chi_tiet_nhoms,id',
            'message'          => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id_nhom_du_lich.required'  => 'ID nhóm du lịch là bắt buộc.',
            'id_nhom_du_lich.integer'   => 'ID nhóm du lịch phải là số nguyên.',
            'id_nhom_du_lich.exists'    => 'Nhóm du lịch không tồn tại.',
            'id_chi_tiet_nhom.required' => 'ID chi tiết nhóm là bắt buộc.',
            'id_chi_tiet_nhom.integer'  => 'ID chi tiết nhóm phải là số nguyên.',
            'id_chi_tiet_nhom.exists'   => 'Chi tiết nhóm không tồn tại.',
            'message.required'          => 'Nội dung tin nhắn là bắt buộc.',
            'message.string'            => 'Nội dung tin nhắn phải là chuỗi ký tự.',
        ];
    }
}
