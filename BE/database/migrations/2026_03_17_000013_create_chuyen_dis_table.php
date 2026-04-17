<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chuyen_dis', function (Blueprint $table) {
            $table->id();
            $table->integer('id_nguoi_dung');
            $table->integer('id_nhom_du_lich')->nullable();
            $table->string('ten_chuyen_di');
            $table->integer('so_ngay')->default(1);
            $table->integer('so_nguoi')->default(1);
            $table->decimal('ngan_sach', 15, 2)->nullable()->comment('Ngân sách dự kiến');
            $table->date('ngay_bat_dau')->nullable();
            $table->integer('trang_thai')->default(1)->comment('1: Đang lên kế hoạch, 2: Đang đi, 3: Đã hoàn thành, 0: Hủy');
            $table->timestamps();

            // $table->foreign('id_nguoi_dung')->references('id')->on('nguoi_dungs')->onDelete('cascade');
            // $table->foreign('id_nhom_du_lich')->references('id')->on('nhom_du_lichs')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chuyen_dis');
    }
};
