<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaDiem extends Model
{
    protected $table = 'dia_diems';

    protected $fillable = [
        'ten_dia_diem',
        'mo_ta',
        'dia_chi',
        'vi_do',
        'kinh_do',
        'gia_ve',
        'gio_mo_cua',
        'gio_dong_cua',
        'danh_gia_trung_binh',
        'loai_dia_diem',
        'danh_muc',
    ];

    protected $appends = ['image'];

    public function getImageAttribute()
    {
        $mainImage = $this->gallery()->where('is_main', true)->first();
        if ($mainImage) {
            return $mainImage->duong_dan_anh;
        }
        
        $firstImage = $this->gallery()->first();
        return $firstImage ? $firstImage->duong_dan_anh : null;
    }

    public function gallery()
    {
        return $this->hasMany(HinhAnhDiaDiem::class, 'id_dia_diem');
    }

    public function danhGias()
    {
        return $this->hasMany(DanhGia::class, 'id_dia_diem');
    }

    public function yeuThich()
    {
        return $this->hasMany(DiaDiemYeuThich::class, 'id_dia_diem');
    }
}
