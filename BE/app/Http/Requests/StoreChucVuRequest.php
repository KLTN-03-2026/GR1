<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChucVuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_chuc_vu' => 'required|string|max:255|unique:chuc_vus,ten_chuc_vu',
            'tinh_trang' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_chuc_vu.required' => 'Tên chức vụ là bắt buộc.',
            'ten_chuc_vu.string' => 'Tên chức vụ phải là chuỗi ký tự.',
            'ten_chuc_vu.max' => 'Tên chức vụ không được vượt quá 255 ký tự.',
            'ten_chuc_vu.unique' => 'Tên chức vụ đã tồn tại.',
            'tinh_trang.integer' => 'Tình trạng phải là số.',
            'tinh_trang.in' => 'Tình trạng chỉ có thể là 0 hoặc 1.',
        ];
    }
}
