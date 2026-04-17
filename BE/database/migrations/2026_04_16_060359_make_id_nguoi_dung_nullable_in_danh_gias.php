<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Cho phép id_nguoi_dung = NULL để lưu đánh giá crawled từ Google Maps
     * (những đánh giá này không có tài khoản trong hệ thống)
     */
    public function up(): void
    {
        Schema::table('danh_gias', function (Blueprint $table) {
            $table->unsignedBigInteger('id_nguoi_dung')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('danh_gias', function (Blueprint $table) {
            // Chú ý: chỉ rollback được nếu không có data null
            $table->unsignedBigInteger('id_nguoi_dung')->nullable(false)->change();
        });
    }
};

