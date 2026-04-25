<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDanhGiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_nguoi_dung' => 'sometimes|required|integer|exists:nguoi_dungs,id',
            'id_dia_diem'   => 'sometimes|required|integer|exists:dia_diems,id',
            'so_sao'        => 'sometimes|required|integer|between:1,5',
            'noi_dung'      => 'nullable|string',
            'trang_thai'    => 'sometimes|required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'id_nguoi_dung.required' => 'ID người dùng là bắt buộc.',
            'id_nguoi_dung.integer'  => 'ID người dùng phải là số nguyên.',
            'id_nguoi_dung.exists'   => 'Người dùng không tồn tại.',
            'id_dia_diem.required'   => 'ID địa điểm là bắt buộc.',
            'id_dia_diem.integer'    => 'ID địa điểm phải là số nguyên.',
            'id_dia_diem.exists'     => 'Địa điểm không tồn tại.',
            'so_sao.required'        => 'Số sao đánh giá là bắt buộc.',
            'so_sao.integer'         => 'Số sao phải là số nguyên.',
            'so_sao.between'         => 'Số sao phải nằm trong khoảng từ 1 đến 5.',
            'noi_dung.string'        => 'Nội dung phải là chuỗi ký tự.',
        ];
    }
}
