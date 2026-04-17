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
        Schema::table('dia_diems', function (Blueprint $table) {
            // Đổi cột image sang kiểu text vì URL Google Images có thể rất dài (> 255 kí tự)
            $table->text('image')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dia_diems', function (Blueprint $table) {
            $table->string('image', 255)->nullable()->change();
        });
    }
};
