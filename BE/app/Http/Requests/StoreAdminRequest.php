<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'ho_ten' => $this->input('ho_ten', $this->input('ten')),
            'mat_khau' => $this->input('mat_khau', $this->input('password')),
            'id_chuc_vu' => $this->input('id_chuc_vu', $this->input('chuc_vu')),
            'trang_thai' => $this->input('trang_thai', $this->input('trang_thai_hoat_dong')),
            'email' => $this->filled('email') ? strtolower($this->input('email')) : $this->input('email'),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admin,email',
            'mat_khau' => 'required|string|min:6',
            'id_chuc_vu' => 'required|integer|exists:chuc_vus,id',
            'so_dien_thoai' => ['required', 'regex:/^(0|\+84)\d{9}$/'],
            'trang_thai' => 'required|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'ho_ten.required' => 'Họ tên là bắt buộc.',
            'ho_ten.string' => 'Họ tên phải là chuỗi.',
            'ho_ten.max' => 'Họ tên không được vượt quá 255 ký tự.',
            'email.required' => 'Email là bắt buộc.',
            'email.string' => 'Email phải là chuỗi.',
            'email.email' => 'Email không đúng định dạng.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            'email.unique' => 'Email đã tồn tại.',
            'mat_khau.required' => 'Mật khẩu là bắt buộc.',
            'mat_khau.string' => 'Mật khẩu phải là chuỗi.',
            'mat_khau.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'id_chuc_vu.required' => 'Chức vụ là bắt buộc.',
            'id_chuc_vu.integer' => 'ID chức vụ phải là số nguyên.',
            'id_chuc_vu.exists' => 'Chức vụ không tồn tại.',
            'so_dien_thoai.required' => 'Số điện thoại là bắt buộc.',
            'so_dien_thoai.regex' => 'Số điện thoại không hợp lệ.',
            'trang_thai.required' => 'Trạng thái hoạt động là bắt buộc.',
            'trang_thai.integer' => 'Trạng thái phải là số nguyên.',
            'trang_thai.in' => 'Trạng thái không hợp lệ.',
        ];
    }
}
