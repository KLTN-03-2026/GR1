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
        Schema::create('chi_phi_phat_sinhs', function (Blueprint $table) {
            $table->id();
            $table->integer('id_chuyen_di');
            $table->string('noi_dung');
            $table->decimal('tong_chi_phi', 15, 2); // Khai báo tối đa 15 chữ số, trong đó 2 chữ số thập phân
            $table->timestamps();
            
            // Có thể thêm foreign keys sau
            // $table->foreign('id_chuyen_di')->references('id')->on('chuyen_dis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_phi_phat_sinhs');
    }
};
