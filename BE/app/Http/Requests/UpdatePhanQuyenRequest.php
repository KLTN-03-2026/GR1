<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhanQuyenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_chuc_vu' => 'sometimes|required|integer',
            'id_chuc_nang' => 'sometimes|required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'id_chuc_vu.required' => 'ID chức vụ là bắt buộc.',
            'id_chuc_vu.integer' => 'ID chức vụ phải là số nguyên.',
            'id_chuc_nang.required' => 'ID chức năng là bắt buộc.',
            'id_chuc_nang.integer' => 'ID chức năng phải là số nguyên.',
        ];
    }
}
