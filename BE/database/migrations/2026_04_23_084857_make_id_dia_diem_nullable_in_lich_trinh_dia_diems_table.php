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
            $table->integer('id_dia_diem')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lich_trinh_dia_diems', function (Blueprint $table) {
            $table->integer('id_dia_diem')->nullable(false)->change();
        });
    }
};
