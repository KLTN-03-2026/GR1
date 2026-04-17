<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nguoi_dungs', function (Blueprint $table) {
            $table->boolean('is_active')->default(0)->after('anh_dai_dien');
            $table->uuid('hash_active')->nullable()->unique()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('nguoi_dungs', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'hash_active']);
        });
    }
};
