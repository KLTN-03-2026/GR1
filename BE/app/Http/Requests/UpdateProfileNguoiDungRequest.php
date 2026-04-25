<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileNguoiDungRequest extends FormRequest
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
            'ten' => 'required|string|max:255',
            'so_dien_thoai' => ['required', 'regex:/^0[0-9]{9}$/'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'ten.required' => 'Tên không được để trống.',
            'ten.string' => 'Tên không hợp lệ.',
            'ten.max' => 'Tên không được vượt quá 255 ký tự.',
            'so_dien_thoai.required' => 'Số điện thoại không được để trống.',
            'so_dien_thoai.regex' => 'Số điện thoại không hợp lệ (bắt đầu 0 và 10 chữ số).',
        ];
    }
}
