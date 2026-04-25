<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class NhomChat extends Model
{
    protected $table = 'nhom_chats';

    protected $fillable = [
        'id_nhom_du_lich',
        'id_chi_tiet_nhom',
        'message',
    ];

    public function chiTietNhom(): BelongsTo
    {
        return $this->belongsTo(ChiTietNhom::class, 'id_chi_tiet_nhom');
    }
}
