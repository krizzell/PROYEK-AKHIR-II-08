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
        // Isi nilai NULL dengan NIP auto-generated
        DB::statement("UPDATE guru SET nip_guru = CONCAT('NIP', DATE_FORMAT(NOW(), '%Y%m%d'), LPAD(id_guru, 5, '0')) WHERE nip_guru IS NULL");
        
        Schema::table('guru', function (Blueprint $table) {
            // Modifikasi kolom nip_guru menjadi required dan unique
            $table->string('nip_guru', 30)->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru', function (Blueprint $table) {
            // Kembalikan ke nullable
            $table->string('nip_guru', 30)->nullable()->change();
        });
    }
};

