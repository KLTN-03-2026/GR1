<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChucVu extends Model
{
    use HasFactory;

    protected $table = 'chuc_vus';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ten_chuc_vu',
        'slug_chuc_vu',
        'mo_ta',
        'tinh_trang',
    ];

    public function chucNangs()
    {
        return $this->belongsToMany(ChucNang::class, 'phan_quyens', 'id_chuc_vu', 'id_chuc_nang');
    }
}
