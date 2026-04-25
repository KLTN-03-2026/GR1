<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Đổi kiểu gio_bat_dau từ INT sang VARCHAR(5) để lưu HH:MM
        DB::statement("ALTER TABLE `lich_trinh_dia_diems` MODIFY `gio_bat_dau` VARCHAR(5) NULL COMMENT 'Giờ bắt đầu tham quan (HH:MM)'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `lich_trinh_dia_diems` MODIFY `gio_bat_dau` INT NULL COMMENT 'Thời gian dự kiến tham quan (phút)'");
    }
};
