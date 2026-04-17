<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dia_diems', function (Blueprint $table) {
            $table->id();
            $table->string('ten_dia_diem');
            $table->text('mo_ta')->nullable();
            $table->string('dia_chi')->nullable();
            $table->decimal('vi_do', 10, 7)->nullable()->comment('Vĩ độ (latitude)');
            $table->decimal('kinh_do', 10, 7)->nullable()->comment('Kinh độ (longitude)');
            $table->decimal('gia_ve', 15, 2)->nullable()->comment('Giá vé tham quan');
            $table->time('gio_mo_cua')->nullable()->comment('Giờ mở cửa');
            $table->time('gio_dong_cua')->nullable()->comment('Giờ đóng cửa');
            $table->decimal('danh_gia_trung_binh', 3, 1)->default(0)->comment('Điểm đánh giá trung bình (0.0 - 5.0)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dia_diems');
    }
};
