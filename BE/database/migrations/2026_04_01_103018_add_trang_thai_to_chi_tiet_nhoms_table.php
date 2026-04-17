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
        Schema::table('chi_tiet_nhoms', function (Blueprint $table) {
            $table->tinyInteger('trang_thai')->default(0)->after('vai_tro')
                  ->comment('0: chờ xác nhận, 1: đã tham gia, 2: từ chối');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chi_tiet_nhoms', function (Blueprint $table) {
            $table->dropColumn('trang_thai');
        });
    }
};
