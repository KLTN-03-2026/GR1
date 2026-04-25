<?php

use App\Models\ChiTietNhom;
use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

Broadcast::channel('nhom-chat.{nhomDuLichId}', function ($user, int $nhomDuLichId) {
    return ChiTietNhom::query()
        ->where('id_nhom_du_lich', $nhomDuLichId)
        ->where('id_nguoi_dung', $user->id)
        ->where('trang_thai', 1)
        ->exists();
});
