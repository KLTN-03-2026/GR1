<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietDanhMuc extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_danh_mucs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_danh_muc',
        'id_dia_diem',
    ];
}
