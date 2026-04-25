<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginNguoiDungRequest extends FormRequest
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
            'email'    => 'required|email',
            'mat_khau' => 'required|string'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.required'    => 'Email là bắt buộc.',
            'email.email'       => 'Email không đúng định dạng.',
            'mat_khau.required' => 'Mật khẩu là bắt buộc.'
        ];
    }
}
