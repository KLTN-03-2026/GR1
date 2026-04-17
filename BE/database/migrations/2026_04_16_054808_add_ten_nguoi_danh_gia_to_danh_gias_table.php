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
        Schema::table('danh_gias', function (Blueprint $table) {
            $table->string('ten_nguoi_danh_gia')->nullable()->after('id_nguoi_dung')
                  ->comment('Tên người dùng từ Google Maps (dùng cho review crawled)');
            $table->string('avatar_nguoi_danh_gia')->nullable()->after('ten_nguoi_danh_gia')
                  ->comment('Avatar URL từ Google Maps');
            $table->boolean('la_danh_gia_google')->default(false)->after('avatar_nguoi_danh_gia')
                  ->comment('True = review crawled từ Google Maps');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('danh_gias', function (Blueprint $table) {
            $table->dropColumn(['ten_nguoi_danh_gia', 'avatar_nguoi_danh_gia', 'la_danh_gia_google']);
        });
    }
};
