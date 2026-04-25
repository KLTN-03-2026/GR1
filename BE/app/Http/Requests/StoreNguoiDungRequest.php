<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreNguoiDungRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'ho_va_ten' => $this->input('ho_va_ten', $this->input('ten')),
            'password' => $this->input('password', $this->input('mat_khau')),
            'password_confirmation' => $this->input('password_confirmation', $this->input('mat_khau_confirmation')),
        ]);
    }

    public function rules(): array
    {
        return [
            'ho_va_ten' => 'required|string|max:255',
            'email' => 'required|email|unique:nguoi_dungs,email',
            'so_dien_thoai' => ['required', 'regex:/^(0|\+84)\d{9}$/'],
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
            'anh_dai_dien' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'ho_va_ten.required' => 'Họ và tên không được để trống.',
            'ho_va_ten.string' => 'Họ và tên không hợp lệ.',
            'ho_va_ten.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã tồn tại.',
            'so_dien_thoai.required' => 'Số điện thoại không được để trống.',
            'so_dien_thoai.regex' => 'Số điện thoại không hợp lệ.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password_confirmation.required' => 'Xác nhận mật khẩu không được để trống.',
            'password_confirmation.min' => 'Xác nhận mật khẩu phải có ít nhất 6 ký tự.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors()->toArray();

        if (isset($errors['password'])) {
            $passwordErrors = array_filter(
                $errors['password'],
                fn (string $message) => $message === 'Xác nhận mật khẩu không khớp.'
            );

            if (!empty($passwordErrors)) {
                $errors['password_confirmation'] = array_values(array_unique([
                    ...($errors['password_confirmation'] ?? []),
                    ...$passwordErrors,
                ]));

                $errors['password'] = array_values(array_filter(
                    $errors['password'],
                    fn (string $message) => $message !== 'Xác nhận mật khẩu không khớp.'
                ));

                if (empty($errors['password'])) {
                    unset($errors['password']);
                }
            }
        }

        throw new HttpResponseException(response()->json([
            'message' => 'Dữ liệu không hợp lệ.',
            'errors' => $errors,
        ], 422));
    }
}
