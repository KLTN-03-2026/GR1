<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNhomDuLichRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_tao_nhom' => 'sometimes|required|integer|exists:nguoi_dungs,id',
            'ten_nhom'    => 'sometimes|required|string|max:255',
            'nguoi_tao'   => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'id_tao_nhom.required' => 'ID người tạo nhóm là bắt buộc.',
            'id_tao_nhom.integer'  => 'ID người tạo nhóm phải là số nguyên.',
            'id_tao_nhom.exists'   => 'Người dùng không tồn tại.',
            'ten_nhom.required'    => 'Tên nhóm là bắt buộc.',
            'ten_nhom.string'      => 'Tên nhóm phải là chuỗi ký tự.',
            'ten_nhom.max'         => 'Tên nhóm không được vượt quá 255 ký tự.',
            'nguoi_tao.string'     => 'Người tạo phải là chuỗi ký tự.',
        ];
    }
}
