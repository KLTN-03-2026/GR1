<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHinhAnhDiaDiemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_dia_diem'   => 'required|integer|exists:dia_diems,id',
            'duong_dan_anh' => 'required|string|max:500',
            'is_main'      => 'sometimes|boolean',
            'sort_order'   => 'sometimes|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'id_dia_diem.required'   => 'ID địa điểm là bắt buộc.',
            'id_dia_diem.integer'    => 'ID địa điểm phải là số nguyên.',
            'id_dia_diem.exists'     => 'Địa điểm không tồn tại.',
            'duong_dan_anh.required' => 'Đường dẫn ảnh là bắt buộc.',
            'duong_dan_anh.string'   => 'Đường dẫn ảnh phải là chuỗi ký tự.',
            'duong_dan_anh.max'      => 'Đường dẫn ảnh không được vượt quá 500 ký tự.',
            'is_main.boolean'        => 'is_main phải là true hoặc false.',
            'sort_order.integer'     => 'sort_order phải là số nguyên.',
        ];
    }
}
