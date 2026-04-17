<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nhom_du_lichs', function (Blueprint $table) {
            $table->id();
            $table->integer('id_tao_nhom');
            $table->string('ten_nhom');
            $table->string('nguoi_tao')->nullable();
            $table->timestamps();

            // $table->foreign('id_tao_nhom')->references('id')->on('nguoi_dungs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nhom_du_lichs');
    }
};
