<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('danh_gia_he_thong', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('muc_do_hai_long')->unsigned(); // 1-5
            $table->text('noi_dung')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('danh_gia_he_thong');
    }
};
