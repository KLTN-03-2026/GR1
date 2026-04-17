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
        Schema::create('admin', function (Blueprint $table) {
            $table->id();
            $table->string('ho_ten');
            $table->string('email')->unique();
            $table->string('mat_khau');
            $table->integer('id_chuc_vu')->nullable();
            $table->string('so_dien_thoai')->nullable();
            $table->integer('trang_thai')->default(1)->comment('1: Hoạt động, 0: Khóa');
            $table->timestamps();
            
            // Nếu có bảng chuc_vu thì bỏ comment dòng dưới
            // $table->foreign('id_chuc_vu')->references('id')->on('chuc_vu')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
