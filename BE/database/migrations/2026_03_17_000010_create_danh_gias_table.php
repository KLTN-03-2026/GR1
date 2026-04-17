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
        Schema::create('danh_gias', function (Blueprint $table) {
            $table->id();
            $table->integer('id_nguoi_dung');
            $table->integer('id_dia_diem');
            $table->tinyInteger('so_sao')->comment('Số sao đánh giá (1 - 5)');
            $table->text('noi_dung')->nullable();
            $table->timestamps();

            // $table->foreign('id_nguoi_dung')->references('id')->on('nguoi_dungs')->onDelete('cascade');
            // $table->foreign('id_dia_diem')->references('id')->on('dia_diems')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danh_gias');
    }
};
