<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ChiTietNhom extends Model
{
    protected $table = 'chi_tiet_nhoms';

    protected $fillable = [
        'id_nguoi_dung',
        'id_nhom_du_lich',
        'vai_tro',
        'trang_thai',
    ];

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'id_nguoi_dung');
    }

    public function nhomDuLich(): BelongsTo
    {
        return $this->belongsTo(NhomDuLich::class, 'id_nhom_du_lich');
    }
}
