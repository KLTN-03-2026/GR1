<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDanhMucRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_danh_muc' => 'required|string|max:255|unique:danh_mucs,ten_danh_muc',
            'mo_ta' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_danh_muc.required' => 'Tên danh mục là bắt buộc.',
            'ten_danh_muc.string' => 'Tên danh mục phải là chuỗi ký tự.',
            'ten_danh_muc.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'ten_danh_muc.unique' => 'Tên danh mục đã tồn tại.',
            'mo_ta.string' => 'Mô tả phải là chuỗi ký tự.',
        ];
    }
}
