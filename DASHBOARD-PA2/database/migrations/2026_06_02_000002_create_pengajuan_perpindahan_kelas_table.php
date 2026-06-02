<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_perpindahan_kelas', function (Blueprint $table) {
            $table->id('id_pengajuan');
            $table->string('nomor_induk_siswa', 20);
            $table->unsignedBigInteger('id_kelas_asal');
            $table->unsignedBigInteger('id_kelas_tujuan');
            $table->unsignedBigInteger('id_guru_pengaju');
            $table->unsignedBigInteger('id_guru_pemroses')->nullable();
            $table->text('alasan');
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('alasan_penolakan')->nullable();
            $table->timestamp('tanggal_pengajuan')->useCurrent();
            $table->timestamp('tanggal_diproses')->nullable();
            $table->timestamps();

            $table->foreign('nomor_induk_siswa')->references('nomor_induk_siswa')->on('siswa')->cascadeOnDelete();
            $table->foreign('id_kelas_asal')->references('id_kelas')->on('kelas')->cascadeOnDelete();
            $table->foreign('id_kelas_tujuan')->references('id_kelas')->on('kelas')->cascadeOnDelete();
            $table->foreign('id_guru_pengaju')->references('id_guru')->on('guru')->cascadeOnDelete();
            $table->foreign('id_guru_pemroses')->references('id_guru')->on('guru')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_perpindahan_kelas');
    }
};
