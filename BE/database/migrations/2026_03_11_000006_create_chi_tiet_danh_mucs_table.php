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
        Schema::create('chi_tiet_danh_mucs', function (Blueprint $table) {
            $table->id();
            $table->integer('id_danh_muc');
            $table->integer('id_dia_diem');
            $table->timestamps();
            
            // Có thể thêm foreign keys sau
            // $table->foreign('id_danh_muc')->references('id')->on('danh_mucs')->onDelete('cascade');
            // $table->foreign('id_dia_diem')->references('id')->on('dia_diems')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_danh_mucs');
    }
};
