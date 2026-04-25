<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lich_trinh_dia_diems', function (Blueprint $table) {
            // Đổi tên thoi_gian_du_kien → gio_bat_dau
            $table->renameColumn('thoi_gian_du_kien', 'gio_bat_dau');

            // Thêm gio_ket_thuc và thoi_luong_phut
            $table->string('gio_ket_thuc', 5)->nullable()->comment('Giờ kết thúc tham quan (HH:MM)')->after('gio_bat_dau');
            $table->integer('thoi_luong_phut')->nullable()->comment('Thời lượng tham quan tính theo phút')->after('gio_ket_thuc');
        });
    }

    public function down(): void
    {
        Schema::table('lich_trinh_dia_diems', function (Blueprint $table) {
            $table->dropColumn(['gio_ket_thuc', 'thoi_luong_phut']);
            $table->renameColumn('gio_bat_dau', 'thoi_gian_du_kien');
        });
    }
};
