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
        // STEP 1: Hapus duplikat tagihan - keep only the latest one (highest id) per siswa-periode combo
        DB::statement('
            DELETE FROM tagihan 
            WHERE id_tagihan NOT IN (
                SELECT id_tagihan FROM (
                    SELECT MAX(id_tagihan) as id_tagihan 
                    FROM tagihan 
                    GROUP BY nomor_induk_siswa, periode
                ) AS latest_per_student
            )
        ');

        // STEP 2: Tambahkan unique constraint untuk (nomor_induk_siswa, periode)
        // Ini memastikan 1 siswa hanya bisa memiliki 1 tagihan per periode
        Schema::table('tagihan', function (Blueprint $table) {
            $table->unique(['nomor_induk_siswa', 'periode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropUnique(['nomor_induk_siswa', 'periode']);
        });
    }
};

