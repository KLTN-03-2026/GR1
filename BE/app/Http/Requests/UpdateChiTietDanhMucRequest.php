<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChiTietDanhMucRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_danh_muc' => 'sometimes|required|integer',
            'id_dia_diem' => 'sometimes|required|integer',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'id_danh_muc.required' => 'ID danh mục là bắt buộc.',
            'id_danh_muc.integer' => 'ID danh mục phải là số nguyên.',
            'id_dia_diem.required' => 'ID địa điểm là bắt buộc.',
            'id_dia_diem.integer' => 'ID địa điểm phải là số nguyên.',
        ];
    }
}
