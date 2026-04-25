<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordNguoiDungRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'mat_khau_cu' => 'required|string',
            'mat_khau_moi' => 'required|string|min:6|different:mat_khau_cu',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'mat_khau_cu.required' => 'Mật khẩu cũ không được để trống.',
            'mat_khau_moi.required' => 'Mật khẩu mới không được để trống.',
            'mat_khau_moi.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'mat_khau_moi.different' => 'Mật khẩu mới phải khác mật khẩu cũ.',
        ];
    }
}
