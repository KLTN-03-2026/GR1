<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhGia extends Model
{
    protected $table = 'danh_gias';

    protected $fillable = [
        'id_nguoi_dung',
        'id_dia_diem',
        'so_sao',
        'noi_dung',
        'trang_thai',
        'ten_nguoi_danh_gia',
        'avatar_nguoi_danh_gia',
        'la_danh_gia_google',
    ];

    protected $casts = [
        'la_danh_gia_google' => 'boolean',
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'id_nguoi_dung');
    }

    public function diaDiem()
    {
        return $this->belongsTo(DiaDiem::class, 'id_dia_diem');
    }
}
