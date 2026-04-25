<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChiPhiPhatSinhRequest extends FormRequest
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
            'id_chuyen_di' => 'sometimes|required|integer',
            'noi_dung' => 'sometimes|required|string|max:255',
            'tong_chi_phi' => 'sometimes|required|numeric|min:0',
            'id_nguoi_tra' => 'nullable|integer',
            'ngay_chi' => 'nullable|date',
            'loai_chi_phi' => 'nullable|string|max:50',
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
            'id_chuyen_di.required' => 'ID chuyến đi là bắt buộc.',
            'id_chuyen_di.integer' => 'ID chuyến đi phải là số nguyên.',
            'noi_dung.required' => 'Nội dung là bắt buộc.',
            'noi_dung.string' => 'Nội dung phải là chuỗi.',
            'noi_dung.max' => 'Nội dung không được vượt quá 255 ký tự.',
            'tong_chi_phi.required' => 'Tổng chi phí là bắt buộc.',
            'tong_chi_phi.numeric' => 'Tổng chi phí phải là số.',
            'tong_chi_phi.min' => 'Tổng chi phí phải lớn hơn hoặc bằng 0.',
        ];
    }
}
