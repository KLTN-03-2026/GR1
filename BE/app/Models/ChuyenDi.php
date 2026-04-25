<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChuyenDi extends Model
{
    protected $table = 'chuyen_dis';

    protected $fillable = [
        'id_nguoi_dung',
        'id_nhom_du_lich',
        'ten_chuyen_di',
        'so_ngay',
        'so_nguoi',
        'ngan_sach',
        'ngay_bat_dau',
        'trang_thai',
    ];
}
