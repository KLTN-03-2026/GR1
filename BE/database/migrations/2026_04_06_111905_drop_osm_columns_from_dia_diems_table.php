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
            if (Schema::hasColumn('dia_diems', 'osm_id')) {
                $table->dropColumn('osm_id');
            }
            if (Schema::hasColumn('dia_diems', 'osm_tags')) {
                $table->dropColumn('osm_tags');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dia_diems', function (Blueprint $table) {
            if (!Schema::hasColumn('dia_diems', 'osm_id')) {
                $table->string('osm_id')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('dia_diems', 'osm_tags')) {
                $table->json('osm_tags')->nullable()->after('danh_muc');
            }
        });
    }
};
