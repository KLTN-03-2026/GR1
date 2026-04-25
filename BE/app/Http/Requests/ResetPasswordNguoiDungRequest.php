<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordNguoiDungRequest extends FormRequest
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
            'key' => 'required|exists:nguoi_dungs,hash_reset',
            'mat_khau_moi' => 'required|string|min:6'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'key.required' => 'Mã xác thực là bắt buộc.',
            'key.exists' => 'Liên kết không hợp lệ hoặc đã hết hạn.',
            'mat_khau_moi.required' => 'Mật khẩu mới không được để trống.',
            'mat_khau_moi.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.'
        ];
    }
}
