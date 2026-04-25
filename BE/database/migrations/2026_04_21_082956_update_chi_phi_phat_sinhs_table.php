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
        Schema::table('chi_phi_phat_sinhs', function (Blueprint $table) {
            $table->unsignedBigInteger('id_nguoi_tra')->nullable()->after('tong_chi_phi');
            $table->date('ngay_chi')->nullable()->after('id_nguoi_tra');
            $table->string('loai_chi_phi')->nullable()->after('ngay_chi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chi_phi_phat_sinhs', function (Blueprint $table) {
            $table->dropColumn(['id_nguoi_tra', 'ngay_chi', 'loai_chi_phi']);
        });
    }
};
