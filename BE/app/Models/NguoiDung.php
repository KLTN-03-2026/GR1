<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class NguoiDung extends Authenticatable
{
    use HasApiTokens;
    protected $table = 'nguoi_dungs';

    protected $fillable = [
        'ten',
        'email',
        'mat_khau',
        'so_dien_thoai',
        'anh_dai_dien',
        'is_active',
        'hash_active',
        'hash_reset',
    ];

    protected $hidden = [
        'mat_khau',
    ];
}
