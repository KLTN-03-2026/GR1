<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvatarNguoiDungRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        \Illuminate\Support\Facades\Log::info('UpdateAvatarRequest incoming', [
            'post' => $this->all(),
            'files' => $this->allFiles(),
            'headers' => $this->headers->all()
        ]);
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'anh_dai_dien' => 'required|image|mimes:png,jpg,jpeg,webp|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'anh_dai_dien.required' => 'Ảnh đại diện là bắt buộc.',
            'anh_dai_dien.image' => 'Ảnh đại diện không hợp lệ.',
            'anh_dai_dien.mimes' => 'Ảnh đại diện phải là png, jpg, jpeg, webp.',
            'anh_dai_dien.max' => 'Ảnh đại diện không được vượt quá 2MB.',
        ];
    }
}
