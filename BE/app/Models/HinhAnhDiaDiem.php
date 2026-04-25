<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HinhAnhDiaDiem extends Model
{
    protected $table = 'hinh_anh_dia_diems';

    protected $fillable = [
        'id_dia_diem',
        'duong_dan_anh',
        'is_main',
        'sort_order',
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'sort_order' => 'integer',
    ];
}
