<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhomDuLich extends Model
{
    protected $table = 'nhom_du_lichs';

    protected $fillable = [
        'id_tao_nhom',
        'ten_nhom',
        'nguoi_tao',
        'mo_ta',
        'id_chuyen_di',
    ];
}
