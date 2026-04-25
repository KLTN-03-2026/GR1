<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChucNangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('chuc_nang');
        return [
            'ten_chuc_nang' => "sometimes|required|string|max:255|unique:chuc_nangs,ten_chuc_nang,{$id}",
        ];
    }

    public function messages(): array
    {
        return [
            'ten_chuc_nang.required' => 'Tên chức năng là bắt buộc.',
            'ten_chuc_nang.string' => 'Tên chức năng phải là chuỗi ký tự.',
            'ten_chuc_nang.max' => 'Tên chức năng không được vượt quá 255 ký tự.',
            'ten_chuc_nang.unique' => 'Tên chức năng đã tồn tại.',
        ];
    }
}
