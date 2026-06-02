<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengumuman', function (Blueprint $table) {
            $table->string('durasi_tampil', 30)->default('7_hari')->after('waktu_unggah');
            $table->dateTime('tampil_sampai')->nullable()->after('durasi_tampil');
        });

        DB::table('pengumuman')
            ->whereNull('tampil_sampai')
            ->update([
                'durasi_tampil' => '7_hari',
                'tampil_sampai' => DB::raw('DATE_ADD(waktu_unggah, INTERVAL 7 DAY)'),
            ]);
    }

    public function down(): void
    {
        Schema::table('pengumuman', function (Blueprint $table) {
            $table->dropColumn(['durasi_tampil', 'tampil_sampai']);
        });
    }
};
