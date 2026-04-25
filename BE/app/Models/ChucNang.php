<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChucNang extends Model
{
    use HasFactory;

    protected $table = 'chuc_nangs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nhom_chuc_nang',
        'ten_chuc_nang',
        'ma_chuc_nang',
    ];

    public function chucVus()
    {
        return $this->belongsToMany(ChucVu::class, 'phan_quyens', 'id_chuc_nang', 'id_chuc_vu');
    }
}
