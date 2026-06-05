<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('akun', function (Blueprint $table) {
            if (!Schema::hasColumn('akun', 'fcm_token')) {
                $table->text('fcm_token')->nullable()->after('is_super_admin');
            }
        });
    }

    public function down(): void
    {
        Schema::table('akun', function (Blueprint $table) {
            if (Schema::hasColumn('akun', 'fcm_token')) {
                $table->dropColumn('fcm_token');
            }
        });
    }
};
