<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dia_diems', function (Blueprint $table) {
            $table->string('image')->nullable()->after('danh_gia_trung_binh')->comment('URL hình ảnh đại diện');
            $table->string('loai_dia_diem')->nullable()->after('image')->comment('Loại địa điểm (Quán ăn, Hải sản, Street food...)');
        });
    }

    public function down(): void
    {
        Schema::table('dia_diems', function (Blueprint $table) {
            $table->dropColumn(['image', 'loai_dia_diem']);
        });
    }
};
