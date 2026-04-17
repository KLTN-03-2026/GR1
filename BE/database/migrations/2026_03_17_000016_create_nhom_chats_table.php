<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nhom_chats', function (Blueprint $table) {
            $table->id();
            $table->integer('id_nhom_du_lich');
            $table->integer('id_chi_tiet_nhom');
            $table->text('message');
            $table->timestamps();

            // $table->foreign('id_nhom_du_lich')->references('id')->on('nhom_du_lichs')->onDelete('cascade');
            // $table->foreign('id_chi_tiet_nhom')->references('id')->on('chi_tiet_nhoms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nhom_chats');
    }
};
