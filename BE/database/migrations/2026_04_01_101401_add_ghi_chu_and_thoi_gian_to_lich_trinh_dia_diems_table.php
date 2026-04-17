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
        Schema::table('lich_trinh_dia_diems', function (Blueprint $table) {
            $table->string('thoi_gian')->nullable()->after('thu_tu_tham_quan');
            $table->text('ghi_chu')->nullable()->after('thoi_gian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lich_trinh_dia_diems', function (Blueprint $table) {
            $table->dropColumn(['thoi_gian', 'ghi_chu']);
        });
    }
};
