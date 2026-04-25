<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSoThichNguoiDungRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_nguoi_dung'    => 'sometimes|required|integer|exists:nguoi_dungs,id',
            'id_danh_muc'      => 'sometimes|required|integer|exists:danh_mucs,id',
            'muc_do_yeu_thich' => 'nullable|integer|between:1,5',
        ];
    }

    public function messages(): array
    {
        return [
            'id_nguoi_dung.required'    => 'ID người dùng là bắt buộc.',
            'id_nguoi_dung.integer'     => 'ID người dùng phải là số nguyên.',
            'id_nguoi_dung.exists'      => 'Người dùng không tồn tại.',
            'id_danh_muc.required'      => 'ID danh mục là bắt buộc.',
            'id_danh_muc.integer'       => 'ID danh mục phải là số nguyên.',
            'id_danh_muc.exists'        => 'Danh mục không tồn tại.',
            'muc_do_yeu_thich.integer'  => 'Mức độ yêu thích phải là số nguyên.',
            'muc_do_yeu_thich.between'  => 'Mức độ yêu thích phải từ 1 đến 5.',
        ];
    }
}
