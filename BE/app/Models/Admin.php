<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'admin';

    protected $fillable = [
        'ho_ten',
        'email',
        'mat_khau',
        'id_chuc_vu',
        'so_dien_thoai',
        'trang_thai',
    ];

    protected $hidden = [
        'mat_khau',
    ];

    protected function casts(): array
    {
        return [
            'mat_khau' => 'hashed',
            'trang_thai' => 'integer',
        ];
    }

    public function chucVu()
    {
        return $this->belongsTo(ChucVu::class, 'id_chuc_vu');
    }

    /**
     * Check if admin has dynamic permission using ma_chuc_nang
     */
    public function hasPermission($ma_chuc_nang)
    {
        // 1. Super Admin bypass (giả sử id_chuc_vu = 1 là Super Admin)
        if ($this->id_chuc_vu === 1) {
            return true;
        }

        // 2. Nếu null hoặc không có chucVu
        if (!$this->chucVu) {
            return false;
        }

        // 3. Load chucNangs if not loaded to check
        $permissions = $this->chucVu->chucNangs->pluck('ma_chuc_nang')->toArray();
        return in_array($ma_chuc_nang, $permissions);
    }
}
