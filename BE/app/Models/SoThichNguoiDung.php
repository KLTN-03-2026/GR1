<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoThichNguoiDung extends Model
{
    protected $table = 'so_thich_nguoi_dungs';

    protected $fillable = [
        'id_nguoi_dung',
        'id_danh_muc',
        'muc_do_yeu_thich',
    ];
}
