<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Hapus siswa dengan NIS kosong
$deleted = DB::table('siswa')
    ->where('nomor_induk_siswa', '')
    ->orWhereNull('nomor_induk_siswa')
    ->delete();

echo "Dihapus: " . $deleted . " record siswa dengan NIS kosong\n";

// Verifikasi
$remaining = DB::table('siswa')->count();
echo "Total siswa setelah perbaikan: " . $remaining . "\n";

echo "\n=== Data Siswa Setelah Perbaikan ===\n";
$siswa = DB::table('siswa')->limit(10)->get();
foreach($siswa as $s) {
    echo "NIS: " . ($s->nomor_induk_siswa ?? 'NULL') . 
         " | Nama: " . ($s->nama_siswa ?? 'NULL') . 
         " | Orangtua: " . ($s->nama_orgtua ?? 'NULL') . "\n";
}
?>
