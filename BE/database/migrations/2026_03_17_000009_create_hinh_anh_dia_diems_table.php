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
        Schema::create('hinh_anh_dia_diems', function (Blueprint $table) {
            $table->id();
            $table->integer('id_dia_diem');
            $table->string('duong_dan_anh');
            $table->timestamps();

            // $table->foreign('id_dia_diem')->references('id')->on('dia_diems')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hinh_anh_dia_diems');
    }
};
