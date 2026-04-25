<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaDiemYeuThich extends Model
{
    protected $table = 'dia_diem_yeu_thich';

    protected $fillable = [
        'id_nguoi_dung',
        'id_dia_diem',
    ];

    public function diaDiem()
    {
        return $this->belongsTo(DiaDiem::class, 'id_dia_diem');
    }

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'id_nguoi_dung');
    }
}
