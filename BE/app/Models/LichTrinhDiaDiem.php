<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichTrinhDiaDiem extends Model
{
    protected $table = 'lich_trinh_dia_diems';

    protected $fillable = [
        'id_chuyen_di',
        'id_dia_diem',
        'thu_tu_tham_quan',
        'thoi_gian',
        'ghi_chu',
        'gio_bat_dau',
        'gio_ket_thuc',
        'thoi_luong_phut',
        'chi_phi_du_kien',
    ];
}
