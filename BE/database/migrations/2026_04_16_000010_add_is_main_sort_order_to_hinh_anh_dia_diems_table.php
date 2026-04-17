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
        Schema::table('hinh_anh_dia_diems', function (Blueprint $table) {
            $table->boolean('is_main')->default(false)->after('duong_dan_anh');
            $table->integer('sort_order')->default(0)->after('is_main');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hinh_anh_dia_diems', function (Blueprint $table) {
            $table->dropColumn(['is_main', 'sort_order']);
        });
    }
};
