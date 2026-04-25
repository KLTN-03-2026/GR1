<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiaDiemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_dia_diem'        => 'required|string|max:255',
            'mo_ta'               => 'nullable|string',
            'dia_chi'             => 'nullable|string|max:500',
            'vi_do'               => 'nullable|numeric|between:-90,90',
            'kinh_do'             => 'nullable|numeric|between:-180,180',
            'gia_ve'              => 'nullable|numeric|min:0',
            'gio_mo_cua'          => 'nullable|date_format:H:i',
            'gio_dong_cua'        => 'nullable|date_format:H:i',
            'danh_gia_trung_binh' => 'nullable|numeric|between:0,5',
            'image'               => 'nullable|string',
            'loai_dia_diem'       => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_dia_diem.required'        => 'Tên địa điểm là bắt buộc.',
            'ten_dia_diem.string'          => 'Tên địa điểm phải là chuỗi ký tự.',
            'ten_dia_diem.max'             => 'Tên địa điểm không được vượt quá 255 ký tự.',
            'mo_ta.string'                 => 'Mô tả phải là chuỗi ký tự.',
            'dia_chi.string'               => 'Địa chỉ phải là chuỗi ký tự.',
            'dia_chi.max'                  => 'Địa chỉ không được vượt quá 500 ký tự.',
            'vi_do.numeric'                => 'Vĩ độ phải là số.',
            'vi_do.between'                => 'Vĩ độ phải nằm trong khoảng -90 đến 90.',
            'kinh_do.numeric'              => 'Kinh độ phải là số.',
            'kinh_do.between'              => 'Kinh độ phải nằm trong khoảng -180 đến 180.',
            'gia_ve.numeric'               => 'Giá vé phải là số.',
            'gia_ve.min'                   => 'Giá vé không được nhỏ hơn 0.',
            'gio_mo_cua.date_format'       => 'Giờ mở cửa phải đúng định dạng HH:MM.',
            'gio_dong_cua.date_format'     => 'Giờ đóng cửa phải đúng định dạng HH:MM.',
            'danh_gia_trung_binh.numeric'  => 'Điểm đánh giá phải là số.',
            'danh_gia_trung_binh.between'  => 'Điểm đánh giá phải nằm trong khoảng 0 đến 5.',
        ];
    }
}
