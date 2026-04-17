<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chi_tiet_nhoms', function (Blueprint $table) {
            $table->id();
            $table->integer('id_nguoi_dung');
            $table->integer('id_nhom_du_lich');
            $table->string('vai_tro')->default('thanh_vien')->comment('truong_nhom, thanh_vien');
            $table->timestamps();

            // $table->foreign('id_nguoi_dung')->references('id')->on('nguoi_dungs')->onDelete('cascade');
            // $table->foreign('id_nhom_du_lich')->references('id')->on('nhom_du_lichs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_nhoms');
    }
};
