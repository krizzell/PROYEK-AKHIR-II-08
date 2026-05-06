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
        Schema::table('guru', function (Blueprint $table) {
            $table->string('nip_guru', 30)->nullable()->after('id_guru')->comment('NIP / ID Guru');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable()->after('nama_guru');
            $table->date('tanggal_lahir')->nullable()->after('jenis_kelamin');
            $table->text('alamat')->nullable()->after('tanggal_lahir');
            $table->enum('jabatan', ['Guru', 'Kepala Sekolah'])->default('Guru')->after('email');
            $table->string('pendidikan_terakhir', 100)->nullable()->after('jabatan')->comment('S1, S2, S3, dll');
            $table->string('jurusan', 100)->nullable()->after('pendidikan_terakhir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru', function (Blueprint $table) {
            $table->dropColumn([
                'nip_guru',
                'jenis_kelamin',
                'tanggal_lahir',
                'alamat',
                'jabatan',
                'pendidikan_terakhir',
                'jurusan'
            ]);
        });
    }
};
