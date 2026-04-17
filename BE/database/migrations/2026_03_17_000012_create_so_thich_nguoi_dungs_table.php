<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('so_thich_nguoi_dungs', function (Blueprint $table) {
            $table->id();
            $table->integer('id_nguoi_dung');
            $table->integer('id_danh_muc');
            $table->tinyInteger('muc_do_yeu_thich')->default(1)->comment('Mức độ yêu thích (1-5)');
            $table->timestamps();

            // $table->foreign('id_nguoi_dung')->references('id')->on('nguoi_dungs')->onDelete('cascade');
            // $table->foreign('id_danh_muc')->references('id')->on('danh_mucs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('so_thich_nguoi_dungs');
    }
};
