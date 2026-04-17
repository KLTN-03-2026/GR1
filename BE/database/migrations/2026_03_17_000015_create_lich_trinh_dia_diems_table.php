<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lich_trinh_dia_diems', function (Blueprint $table) {
            $table->id();
            $table->integer('id_chuyen_di');
            $table->integer('id_dia_diem');
            $table->integer('thu_tu_tham_quan')->default(1)->comment('Thứ tự tham quan trong ngày');
            $table->integer('thoi_gian_du_kien')->nullable()->comment('Thời gian dự kiến tham quan (phút)');
            $table->decimal('chi_phi_du_kien', 15, 2)->nullable()->comment('Chi phí dự kiến');
            $table->timestamps();

            // $table->foreign('id_chuyen_di')->references('id')->on('chuyen_dis')->onDelete('cascade');
            // $table->foreign('id_dia_diem')->references('id')->on('dia_diems')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lich_trinh_dia_diems');
    }
};
