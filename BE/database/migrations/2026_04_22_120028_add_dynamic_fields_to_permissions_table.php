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
        // Add mo_ta to chuc_vus
        Schema::table('chuc_vus', function (Blueprint $table) {
            if (!Schema::hasColumn('chuc_vus', 'mo_ta')) {
                $table->text('mo_ta')->nullable()->after('slug_chuc_vu');
            }
        });

        // Add nhom_chuc_nang and ma_chuc_nang to chuc_nangs
        Schema::table('chuc_nangs', function (Blueprint $table) {
            if (!Schema::hasColumn('chuc_nangs', 'ma_chuc_nang')) {
                $table->string('ma_chuc_nang')->nullable()->unique()->after('ten_chuc_nang');
            }
            if (!Schema::hasColumn('chuc_nangs', 'nhom_chuc_nang')) {
                $table->string('nhom_chuc_nang')->nullable()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('chuc_vus', function (Blueprint $table) {
            if (Schema::hasColumn('chuc_vus', 'mo_ta')) {
                $table->dropColumn('mo_ta');
            }
        });

        Schema::table('chuc_nangs', function (Blueprint $table) {
            if (Schema::hasColumn('chuc_nangs', 'ma_chuc_nang')) {
                $table->dropColumn('ma_chuc_nang');
            }
            if (Schema::hasColumn('chuc_nangs', 'nhom_chuc_nang')) {
                $table->dropColumn('nhom_chuc_nang');
            }
        });
    }
};
