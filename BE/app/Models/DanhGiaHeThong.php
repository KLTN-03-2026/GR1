<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhGiaHeThong extends Model
{
    protected $table = 'danh_gia_he_thong';

    protected $fillable = [
        'muc_do_hai_long',
        'noi_dung',
        'ip_address',
    ];
}
