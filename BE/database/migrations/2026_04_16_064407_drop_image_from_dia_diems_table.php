<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Start by migrating existing image data
        $diaDiems = DB::table('dia_diems')->whereNotNull('image')->where('image', '!=', '')->get();
        foreach ($diaDiems as $diaDiem) {
            DB::table('hinh_anh_dia_diems')->insertOrIgnore([
                'id_dia_diem' => $diaDiem->id,
                'duong_dan_anh' => $diaDiem->image,
                'is_main' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Then drop the column
        Schema::table('dia_diems', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dia_diems', function (Blueprint $table) {
            $table->string('image')->nullable();
        });

        // Copy back data (if needed, this grabs the main image back to dia_diems)
        $hinhAnhs = DB::table('hinh_anh_dia_diems')->where('is_main', true)->get();
        foreach ($hinhAnhs as $hinh) {
            DB::table('dia_diems')
                ->where('id', $hinh->id_dia_diem)
                ->update(['image' => $hinh->duong_dan_anh]);
        }
    }
};
