<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiPhiPhatSinh extends Model
{
    use HasFactory;

    protected $table = 'chi_phi_phat_sinhs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_chuyen_di',
        'noi_dung',
        'tong_chi_phi',
        'id_nguoi_tra',
        'ngay_chi',
        'loai_chi_phi',
    ];

    public function nguoiTra()
    {
        return $this->belongsTo(NguoiDung::class, 'id_nguoi_tra');
    }
}
