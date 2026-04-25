<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterNguoiDungRequest extends FormRequest
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
            'ho_va_ten'     => 'required|string|max:255',
            'email'         => [
                'required',
                'email',
                'unique:nguoi_dungs,email',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/',
            ],
            'so_dien_thoai' => ['required', 'regex:/^0[0-9]{9}$/'],
            'password'      => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string',
            'anh_dai_dien'  => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'ho_va_ten.required'          => 'Họ và tên không được để trống.',
            'ho_va_ten.max'               => 'Họ và tên không được vượt quá 255 ký tự.',
            'email.required'              => 'Email không được để trống.',
            'email.email'                 => 'Email không đúng định dạng.',
            'email.unique'                => 'Email này đã được đăng ký, vui lòng dùng email khác.',
            'email.regex'                 => 'Hệ thống chỉ chấp nhận email có đuôi @gmail.com.',
            'so_dien_thoai.required'      => 'Số điện thoại không được để trống.',
            'so_dien_thoai.regex'         => 'Số điện thoại không hợp lệ (phải bắt đầu bằng số 0 và có 10 chữ số).',
            'password.required'           => 'Mật khẩu không được để trống.',
            'password.min'                => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed'          => 'Xác nhận mật khẩu không khớp.',
            'password_confirmation.required' => 'Vui lòng xác nhận mật khẩu.',
        ];
    }
}
